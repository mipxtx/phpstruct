<?php
/**
 * @author: mix
 * @date: 26.10.14
 */
/*
if (!($oldEmp->can_do('Complaint.Stats') || ($oldEmp->is_root()))) {
    tmpl_set($tplPage, 'PAGE_main', $GLOBALS['ADMIN_DENY']);
    echo tmpl_parse($tplPage);
    // убьем конекты к БД
    include_once PHPWEB_PATH_INIT . "finish_db.inc";
    exit;
}
*/

/*
if ($moderatorIds) {
    foreach ($moderatorIds as $id) {
        $moderatorId[] = $groupPrivChecker->getEmpId($id);
    }
} else {
    foreach ($groupPrivChecker->getEmpList(false) as $moderator) {
        $moderatorId[] = $moderator->getId();
    }
}

*/
//foreach ($res as $v) {
//$moderatorId = implode(",", $moderatorId) ?: 0;
//}

//echo '<style>a { text-decoration:none }</style>' . substr($getoids, -2, 2) . '-' . substr($getoids, 4, 2) . '-'
//    . substr($getoids, 0, 4) . '<table border=1>';

/*if(($part == 'anketa') && ($v['anketas'] > 0) || ($part == 'photo') && ($v['photos_moderated'] > 0)){
    $a =$b;
}*/

/*
$a =[
    'PAGE_title' => $GLOBALS['_STAT_MODERATOR_TITLE'],
    'PAGE_www' => $partnerArray['url']['www'],
];

*/

//$res = $this->setType($anketaData['oid'], $value ? 22 : -22);

//$value ? 22 : -22;

//\Logger\LoggerLocator::get()->error('wrong anketa params', ['params' => $params]);
/*if ($anketaIds) {
    $counters = (new AdminComplaint)->getComplaintTotalCount($anketaIds);

    foreach ($stat as &$row) {
        $row['complaint_total_count'] = isset($counters[$row['anketa_id']]) ? $counters[$row['anketa_id']] : 0;
    }
}*/

$b = 5;
$c = 7;

switch ($a + $b) {
    case $a ? $a : $c :
        return 1;
    case "b" :
        return 2;
    default :
        $a = $b;
        return 3;
}