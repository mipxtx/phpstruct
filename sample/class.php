<?php
/**
 *
 * Название класса: AdminAnketa
 * -----------------------------
 * Модерация анкет и фоток
 *
 * @package     Mediahosting
 * @author      joshua5
 * @di admin_anketa.moderation_facade
 */
class AdminAnketa
{
    use Events_Trait;

    use Db_ConnectionFabricTrait;

    // для анкет obj = Anketa
    /**
     * @var Anketa
     */
    var $obj;

    const BAN_FROM_FLASH_MODER_TPL = 14;
    const BAN_FROM_ANKETA_TEXT_TPL = 17;

    /**
     * Банит анкету
     *
     * @param int $anketaId Идентификатор анкеты
     * @param int $moderatorId Идентификатор модератора
     * @param AdminAnketaRejectReason $rejectReason Причина бана
     * @return bool
     */
    public function rejectAnketa($anketaId, $moderatorId, AdminAnketaRejectReason $rejectReason) {


        switch ($rejectReason->getReason()) {

            case 'informer_spam': // рассылка рекламы и спама
            case 'informer_menace': // пользователь рассылает угрозы и оскорбления

                break;


        }



        return true;
    }



}
