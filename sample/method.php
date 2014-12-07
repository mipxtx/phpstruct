<?php

/**
 * Класс-namespace методов для работы с анкетой.
 * Не воспринимайте код ниже как описание типа объектов Анкета.
 *
 * @package Anketa
 * @version $Id: Anketa.inc 62166 2012-02-16 07:43:04Z raven $
 */
class Anketa extends _Object
{
    /**
     * Обновление анкеты
     *
     * @param array $params
     *
     * @return bool
     */
    public function update($params) {

        // начнем транзакцию
        if (!ConnectionManager::begin()) {
            ConnectionManager::rollback();

            return false;
        }

        if (!is_array($params)) {
            \Logger\LoggerLocator::get()->error('wrong anketa params', ['params' => $params]);

            return false;
        }

        $anketa = $this->getCached($params['oid']);

        if ((isset($params['type']) && (strpos($params['type'], '|(1<<4)') || strpos($params['type'], '|(1<<1)')))
            || (isset($params['anketa_personal_moderated']) && $params['anketa_personal_moderated'] == 'Rejected')
        ) {

            /**
             * #38550
             * Не сбрасывать хитлист и рейтинг топ100
             */
            //$Rating = new Rating2_VS();
            //нам нужен user_id, так что кэшированная анкета или нет, нам не важно.
            //memcache для анкет очищается после вызова update
            //            $ank = $this->getCached($params['oid']);
            //            if (is_array($ank) && $ank['user_id']) {
            //                if (!$Rating->deleteUserFromRatings($ank['user_id'])) {
            //                    Errors::stderrLog('cant delete rating user: ' . $ank['user_id'], false);
            //                }
            //            }

            if ((isset($params['type']) && (strpos($params['type'], '|(1<<4)') || strpos($params['type'], '|(1<<1)')))
                || (isset($params['status']) && $params['system'] == 'Blocked')
                || (isset($params['system_status']) && $params['system_status'] == 'Blocked')
            ) {

                /**
                 * #38550
                 * Не сбрасывать хитлист и рейтинг топ100
                 */
                //Hits_Helper::deleteUserClick($params['oid']);

                // Очистим очередь неотправленных сообщений
                if (!class_exists('MNotify')) {
                    include_once PHPWEB_PATH_PACKAGES . 'MNotify/_package.inc';
                }

                if (!is_array($anketa) || !MNotify::clean($anketa['user_id'])) {
                    // MNotify падает часто и засирает логи
                    //error_log( 'anketa->update: Не смогли удалить очередь сообщений MNotify' );
                }
            }
        }

        $this->fixLangs($params);

        $this->logNullCountryId($params);
        $result = parent::update($params);
        if (!$result || !$result['result']) {
            ConnectionManager::rollback();

            return false;
        }

        // закончим транзакцию
        if (!ConnectionManager::commit()) {
            ConnectionManager::rollback();

            return false;
        }

        return $result;
    }


}
