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

/*function a($a) {
    $b = 5;
    $c = 7;


    switch ($a + $b) {
        case 0:
        case $a ? $a : $c :
            return 1;
        case "b" :
            return 2;
        default :
            $a = $b;

            return 3;
    }
}
*/

/*try{
    $a  =+ $b;
}catch(\PhpDump\FailException $e){

}*/
/*
$this->addArray(
    [
        'COMPLAIN' => [
            'kind' => $this->complaintType,
            'anketa_id' => $this->contactAnketa['oid'],
            'entity_id' => $this->entity_id,
            'VOLONTER' => Anketa_AgentHelper::isAgent()
                && !Anketa_AgentHelper::isVipUser(
                    $this->contactAnketa['oid']
                ),
            'OPTION' => $this->getReasons()
        ]
    ]
);
*/

//
/*
$parentIds[] = (int)$parentId;

$parentIds[1]  = 4;
$parentIds = [567, "fff"];
*/

//use BillingSubscribe_Events_Event as Event,    BillingSubscribe_Aggregator_Data_BaseObject as SubscribeObject;

//"{{$reasonCode}}";
//"{$reasonCode}";

/*

$rr = "Select {$skip_sql->call()}";

$res =
    <<<SQL
            SELECT *
  FROM CorpCMS.cms_feeds
  WHERE host = s:host
    AND lang = s:lang
    {$skip_sql->call()}
SQL;

*/

//$res = "HS_$this->name";
//$res = 1;
//$res = "HS_{$connectName}";

//$cmd = "svn add {$pathPrefix}$modelFileName";
//echo $cmd . "\n";
//echo `{$cmd}`;

//$t = Stat_Btp_Logger::getInstance()->add("HS_{$connectName}", "execute");


//$this->namespace = $namespace;

//"$a[1]$a[2]${${uri}}$a[2]";

//"LANG_MOBILE_M\\push\\${type}";

//$geo = (new \Front_HttpRequest)->getGeoLocation();

//$a = "3\n";$uri = "a";$p = ${uri};


//$details['required'] AND empty($this->settings[$option]);
/*
declare(ticks = 1){
    foreach ($this->pids as $pid) {
        pcntl_waitpid(0, $status);
        if ($status != 0) {
            //varlog("Terminating...");
            $this->signal_handler(null);
            break;
        }
    }
}
*/


//

//"$this->errno($this->errstr) $a[4]\n";
/*if ( $l==0 )
    $h = 4294967296.0 - $h;
else
{
    $h = 4294967295.0 - $h;
    $l = 4294967296.0 - $l;
}*/

//$formulaStrings[] = "$space1$space0$function(" . implode(',', $ops) . ")";

/*
if (0 === strpos($pathinfo, '/bar')) {
    // bar
    if (preg_match('#^/bar/(?P<foo>[^/]++)$#s', $pathinfo, $matches)) {
        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
            $allow = array_merge($allow, array('GET', 'HEAD'));
            goto not_bar;
        }

        return $this->mergeDefaults(array_replace($matches, array('_route' => 'bar')), array ());
    }
    not_bar:

    // barhead
    if (0 === strpos($pathinfo, '/barhead') && preg_match('#^/barhead/(?P<foo>[^/]++)$#s', $pathinfo, $matches)) {
        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
            $allow = array_merge($allow, array('GET', 'HEAD'));
            goto not_barhead;
        }

        return $this->mergeDefaults(array_replace($matches, array('_route' => 'barhead')), array ());
    }
    not_barhead:

}
*/

//

$a = 6*5*4+3+2+1 == 7+2+5+6;

//1+2+3;

//3 * +2;