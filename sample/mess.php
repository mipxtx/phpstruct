<?php
require __DIR__ . '/../init.php';
include_once PHPWEB_PATH_INIT . "global_init.inc";
include_once PHPWEB_PATH_INIT . "staff/auth_new.inc";
include_once PHPWEB_PATH_INIT . "staff/base_init.inc";

$tplPage = tmpl_open($oldTpl->filename('admin/admin.page.frame.main.tpl'), $oldTpl->config);
$tplList = tmpl_open($oldTpl->filename('admin/admin.page.stat_complaint.tpl'), $oldTpl->config);
$tplSelect = tmpl_open($oldTpl->filename('block.select.tpl'), $oldTpl->config);

include_once PHPWEB_PATH_INIT . "staff/base.inc";

tmpl_set($tplPage, $oldTpl->globalVars);
tmpl_set($tplList, $oldTpl->globalVars);

// статистика модерации анкет
include_once PHPWEB_PATH_PACKAGES . "AdminAnketa/_package.inc";

$oldLanguage->languageVars("Dates");
$oldLanguage->languageVars("AdminStat");
$oldLanguage->languageVars("Admin");
$oldLanguage->languageVars("Anketa");
$oldLanguage->languageVars("ComplaintReason");

// администрирование анкет
$adminAnketa = new AdminAnketa();

$adminUser = ['oid' => $oldEmp->id(), 'language_id' => 0, 'partner_id' => 0];
// права на просмотр
if (!($oldEmp->can_do('Complaint.Stats') || ($oldEmp->is_root()))) {
    tmpl_set($tplPage, 'PAGE_main', $GLOBALS['ADMIN_DENY']);
    echo tmpl_parse($tplPage);
    // убьем конекты к БД
    include_once PHPWEB_PATH_INIT . "finish_db.inc";
    exit;
}

$month = (int)$oldVars->get('month');
$year = (int)$oldVars->get('year');
$getoids = $oldVars->get('getoids');
$part = $oldVars->get('part');

if (!$year) {
    $year = date('Y');
}
if (!$month) {
    $month = date('m');
}

$month0 = sprintf('%02d', $month);
$year0 = sprintf('%04d', $year);

$groupPrivChecker = new Staff2_GroupPrivChecker($oldEmp);
$moderatorIds = explode(",", $oldVars->get('moderator_id'));
$moderatorIds = array_filter($moderatorIds);
$moderatorId = [];

if ($moderatorIds) {
    foreach ($moderatorIds as $id) {
        $moderatorId[] = $groupPrivChecker->getEmpId($id);
    }
} else {
    foreach ($groupPrivChecker->getEmpList(false) as $moderator) {
        $moderatorId[] = $moderator->getId();
    }
}

$moderatorId = implode(",", $moderatorId) ? : 0;

if ($getoids) {
    $hour = $oldVars->get('hour');
    $status = $oldVars->get('status');
    $notStatus = $oldVars->get('not_status');

    //
    $statFrom = $getoids . sprintf('%02d0000', strlen($hour) ? (int)$hour: 0);
    $statTo = $getoids . sprintf('%02d5959', strlen($hour) ? (int)$hour: 23);

    $res = $adminAnketa->getComplaintStatisticsOids($moderatorId, $statFrom, $statTo, $status, $notStatus);

    $created = '';

    $wasPrinted = [];

    echo '<style>a { text-decoration:none }</style>' . substr($getoids, -2, 2) . '-' . substr($getoids, 4, 2) . '-' . substr($getoids, 0, 4) . '<table border=1>';

    foreach ($res as $v) {
        if ((($part == 'anketa') && ($v['anketas'] > 0)) || (($part == 'photo') && ($v['photos_moderated'] > 0))) {
            if ($created != $v['tm']) {
                if ($created == '') {
                    echo '<tr>';
                } else {
                    echo '</td>';
                }

                echo '<td valign=top>';
                $created = $v['tm'];
                echo substr($created, -2, 2) . '<br>';
            }

            echo '<br>';
            echo '<a href="/support/complains.phtml?search_option=id&amp;search_value=' . $v['anketa_id'] . '" target="_blank">' . $v['anketa_id'] . '</a>';
            echo '<sup style="color:red">' . $v['complaint_total_count'] . '</sup> ';
            if ($part == 'photo') {
                echo '<small>(' . $v['photos_moderated'] . ')</small>';
            }
            echo ' <a href="' . Front_UrlGen::getAnketaUrlById($v['anketa_id']) . '" target="_blank">*</a>';

            if ($v['status'] == "clear" || $v['status'] == "delete") {
                $status = Language_Helper::getText('Staff', 'AdminstatComplaint\\' . $v['status']);
            } else {
                $status = $GLOBALS['_COMPLAINT_REASONS'][$v['status']];
            }

            if ($v['name'] == "") {
                $v['name'] = Language_Helper::getText('Staff', 'AdminstatComplaint\\default_group');
            }
            echo '( ' . $status . ' / ' . $v['name'] . ' )';
        }
    }
    echo '</td></tr></table>';
    exit;
}

if (!($oldEmp->is_root() || $oldEmp->can_do('Anketa.moderatedlog'))) {
    $isadmin = false;
} else {
    $isadmin = true;
}

tmpl_set($tplPage, ['PAGE_title' => $GLOBALS['_STAT_MODERATOR_TITLE'], 'PAGE_www' => $partnerArray['url']['www']]);

// месяц
tmpl_set($tplSelect, '/SELECT_name', 'month');
foreach ($_MONTHS as $k => $v) {
    $oldTpl->block($tplSelect, '/SELECT_ITEM');
    tmpl_set($tplSelect, '/', ['/SELECT_ITEM/SELECT_ITEM_value' => sprintf('%02d', $k), '/SELECT_ITEM/SELECT_ITEM_selected' => ((int)$k == (int)$month) ? ' selected': '', '/SELECT_ITEM/SELECT_ITEM_name' => $v['name1']]);
}
tmpl_set($tplList, '/STAT_SELECT_month', tmpl_parse($tplSelect));
tmpl_unset($tplSelect);

// месяц
tmpl_set($tplSelect, '/SELECT_name', 'year');
for($i = 2004; $i <= date('Y'); $i++) {
    $oldTpl->block($tplSelect, '/SELECT_ITEM');
    tmpl_set($tplSelect, '/', ['/SELECT_ITEM/SELECT_ITEM_value' => $i, '/SELECT_ITEM/SELECT_ITEM_selected' => ($i == $year) ? ' selected': '', '/SELECT_ITEM/SELECT_ITEM_name' => $i]);
}
tmpl_set($tplList, '/STAT_SELECT_year', tmpl_parse($tplSelect));
tmpl_unset($tplSelect);

$statFrom = "{$year0}-{$month0}-01";
$statTo = "{$year0}-{$month0}-" . date('t', mktime(0, 0, 0, $month0, 1, $year0)) . ' 23:59:59';

$ulist = [0 => ''];
foreach ($groupPrivChecker->getEmpList() as $item) {

    $statCur = $adminAnketa->getComplaintStatistics($item->getId(), $statFrom, $statTo, $oldLanguage->language_id);

    if ($statCur['total'] == 0) {
        continue;
    }

    $ulist[$item->getId()] = $item->getName();
}

$ma = [];

// модераторы
tmpl_set($tplSelect, '/SELECT_name', 'moderator_id');
foreach ($ulist as $k => $v) {
    $oldTpl->block($tplSelect, '/SELECT_ITEM');
    tmpl_set($tplSelect, '/', ['/SELECT_ITEM/SELECT_ITEM_value' => $k, '/SELECT_ITEM/SELECT_ITEM_selected' => ($k == $moderatorId) ? ' selected': '', '/SELECT_ITEM/SELECT_ITEM_name' => $v]);
    $ma[] = $k;
}

tmpl_set($tplList, '/STAT_SELECT_moderator', tmpl_parse($tplSelect));
tmpl_unset($tplSelect);

if (!$moderatorId) {
    $moderatorId = implode(',', $ma);
}
$messagesMapper = new Staff_Message_DataMapper_Messages($dbh);
$clientMapper = new Staff_Message_DataMapper_Client($dbh);
$clients = [];

$msg_stats = [];
$res = $adminAnketa->getComplaintMessagesStatistics($moderatorId, $statFrom, $statTo);
foreach ($res as $stat) {
    $msg_stats[$stat['created']] = $stat;
}

$stats = $adminAnketa->getComplaintStatistics($moderatorId, $statFrom, $statTo, $oldLanguage->language_id);
$res = [];
$total = 0;

foreach ($stats["data"] as $stat) {
    $timeKeyH = substr($stat["tm"], 6, 2);
    $timeKeyM = substr($stat["tm"], 8, 2);
    if ($timeKeyH[0] === '0') {
        $timeKeyH = substr($timeKeyH, 1);
    }
    if ($timeKeyM[0] === '0') {
        $timeKeyM = substr($timeKeyM, 1);
    }
    $timeKeyH = (int)$timeKeyH;
    $timeKeyM = (int)$timeKeyM;
    if (isset($res[$timeKeyH][$timeKeyM])) {
        $res[$timeKeyH][$timeKeyM]['anketa'] += $stat["c_anketa_id"];
        $res[$timeKeyH][$timeKeyM]['anketa_ids'] += $stat["anketa_ids"];
    } else {
        $res[$timeKeyH][$timeKeyM] = ["anketa" => $stat["c_anketa_id"], "anketa_ids" => $stat["anketa_ids"]];
    }
    $total += $stat["c_anketa_id"];
}

$eres = [];
$etotal = 0;

if (!empty($res)) {
    $totals = ['days' => 0, 'ankets' => 0, 'photos' => 0, 'messages' => 0, 'temporary_problems' => 0];
    $resKeys = array_keys($res);
    sort($resKeys);
    $empIds = array_filter(explode(",", $moderatorId));
    foreach ($resKeys as $k) {
        $v = $res[$k];
        $oldTpl->block($tplList, '/STAT');
        $bgcolor = ((($totals['days'] / 2) == round($totals['days'] / 2)) ? '#f0ffff': '#f0f0f0');
        tmpl_set($tplList, '/STAT/STAT_bgcolor', $bgcolor);

        $totals['days']++;
        $statNum = 0;
        $statPhoto = 0;
        $statPart = 0;

        $amax = $pmax = 0;
        $anketaIds = "";
        for($i = 0; $i < 24; $i++) {
            $kk = $i;
            $i0 = $i;

            $vv = empty($v[$i0]['anketa']) ? 0: $v[$i0]['anketa'];
            $vpa = empty($v[$i0]['part']) ? 0: $v[$i0]['part'];
            $vph = empty($v[$i0]['photo']) ? 0: $v[$i0]['photo'];
            if ($vv > $amax) {
                $amax = $vv;
            }
            if ($vph > $pmax) {
                $pmax = $vph;
            }

            $statNum += $vv;
            $statPhoto += $vph;
            $statPart += $vpa;

            $oldTpl->block($tplList, '/STAT/STAT_time_anket');
            tmpl_set($tplList, '/STAT/STAT_time_anket', ["STAT_time_count" => ($vv ? $vv: '&nbsp;'), "STAT_color" => "#f0f0" . ((255 - $vv) < 16 ? '0' . dechex(255 - $vv): dechex(255 - $vv))]);

            if (isset($v[$i0])) {
                $anketaIds .= (empty($anketaIds) || empty($v[$i0]['anketa_ids']) ? '': ',') . $v[$i0]['anketa_ids'];
            }
        }

        $i_day = sprintf("%02d", $k);

        // anketa:max per hour
        $oldTpl->block($tplList, '/STAT/STAT_time_anket');
        tmpl_set($tplList, '/STAT/STAT_time_anket', ["STAT_time_count" => $amax, "STAT_color" => $bgcolor]);

        $count_temporary_problems = 0;
        $count_msgs = 0;
        if (isset($msg_stats[$i_day . $month0 . $year])) {
            $count_temporary_problems = $msg_stats[$i_day . $month0 . $year]['temporary_problems'];
            $count_msgs = $msg_stats[$i_day . $month0 . $year]['messages_count'];
        }

        $oldTpl->block($tplList, '/STAT/STAT_msg_count');
        tmpl_set($tplList, '/STAT/STAT_msg_count', ['STAT_count_messages' => $count_msgs, 'STAT_count_temporary_problems' => $count_temporary_problems, "STAT_color" => $bgcolor]);

        $day = date('D', mktime(0, 0, 0, $month, $k, $year));
        $day = ($day == 'Sun' ? '<b>' . $k . ' ' . $day . '</b>': $k . ' ' . $day);
        $stat_messages = (isset($v[-1]['messages']) ? $v[-1]['messages']: 0);

        tmpl_set($tplList, '/STAT', ['STAT_color' => "#f0f0f0", 'STAT_day' => $day, 'STAT_date' => $year . $month . $k, 'moderator_id' => $moderatorId, 'STAT_count_anket' => ($isadmin ? '<a target="_blank" href="adminstat_complaint.phtml?getoids=' . $year . $month0 . $i_day . '&part=anketa&moderator_id=' . $moderatorId . '">' . $statNum . '</a>': $statNum), 'STAT_count_photo' => ($isadmin ? '<a target="_blank" href="adminstat_complaint.phtml?getoids=' . $year . $month0 . $i_day . '&part=photo&moderator_id=' . $moderatorId . '">' . $statPhoto . '</a>': $statPhoto)]);

        $totals['temporary_problems'] += $count_temporary_problems;
        $totals['messages'] += $count_msgs;
        $totals['ankets'] += $statNum;
        $totals['photos'] += $statPhoto;
    }

    $oldTpl->block($tplList, '/STAT');
    tmpl_set($tplList, '/STAT/STAT_bgcolor', '#f0ffff');

    for($i = 0; $i < 24; $i++) {
        $oldTpl->block($tplList, '/STAT/STAT_time_anket');
        tmpl_set($tplList, ["STAT_time_count" => '', "STAT_color" => "#ffffff"]);
    }

    $oldTpl->block($tplList, '/STAT/STAT_time_anket');
    tmpl_set($tplList, ["STAT_time_count" => round($totals['ankets'] / $totals['days']), "STAT_color" => "#ffffff"]);

    $oldTpl->block($tplList, '/STAT/STAT_msg_count');
    tmpl_set($tplList, ['STAT_count_messages' => $totals['messages'], 'STAT_count_temporary_problems' => $totals['temporary_problems'], 'STAT_color' => "#ffffff"]);

    tmpl_set($tplList, '/STAT', ["STAT_color" => "#f0d0f0", "STAT_day" => $totals['days'], "STAT_count_anket" => $totals['ankets'], "STAT_count_photo" => $totals['photos']]);
}

tmpl_set($tplPage, 'PAGE_main', tmpl_parse($tplList));
echo tmpl_parse($tplPage);
// убьем конекты к БД
include_once PHPWEB_PATH_INIT . "finish_db.inc";
// сохраним данные в сессию
Finish::finishSession();
