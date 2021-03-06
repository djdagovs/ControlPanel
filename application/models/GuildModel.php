<?php
/**
 * Control Panel
 *
 * Copyright (c) 2013 Efex
 *
 * Панель управления для серверов WoW
 *
 * @author   Efex
 * @license  GNU General Public License
 * @link     https://github.com/Efexis/ControlPanel/
 * @version  0.0.1
 */

class GuildModel extends Model {

    public $message;

    public function searchGuild($guild, $type) {
        $guild = trim($guild);
        if ( preg_match("/^([a-zA-Z\s]{2,24}|[а-яА-ЯёЁ\s]{2,24}|[0-9]{1,10})$/u", $guild) && preg_match("/^[a-zA-Z]+$/u", $type) ) {
            $sql = 'SELECT `c`.`name` as `leader_name`, count(`gm`.`guid`) as `count_member`, `g`.`guildid`, `g`.`name`, `g`.`leaderguid`, `g`.`info`, `g`.`motd`, `g`.`createdate`, `g`.`BankMoney`
                    FROM `'.$this->config['db.char'].'`.`guild` as `g`
                    JOIN `'.$this->config['db.char'].'`.`characters` AS `c` ON `c`.`guid` = `g`.`leaderguid`
                    JOIN `'.$this->config['db.char'].'`.`guild_member` AS `gm` ON `gm`.`guildid` = `g`.`guildid`
                    WHERE `g`.`'.$type.'` = :guild';
            $stmt = $this->db['char']->prepare($sql);
            $stmt->bindValue(':guild', $guild);
            $stmt->execute();
            $guildInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $guildInfo['count_member'] != 0 ) {
                $info = trim($guildInfo['info']);
                $motd = trim($guildInfo['motd']);
                $guildInfo['info'] = empty($info) ? '<i>отсутствует</i>' : $guildInfo['info'];
                $guildInfo['motd'] = empty($motd) ? '<i>отсутствует</i>' : $guildInfo['motd'];
                $guildInfo['BankMoney'] = $this->goldConvert($guildInfo['BankMoney']);
                $guildInfo['createdate'] = date('d.m.Y', $guildInfo['createdate']);
                return  $guildInfo;
            } else {
                $this->message = 'Гильдия с таким названием или id не найдена';
            }
        } else {
            $this->message = 'Введены некорректные данные';
        }
    }

    public function changeGuildName($guild, $name) {
        if ( preg_match("/^[0-9]+$/", $guild) && preg_match("/^([a-zA-Z\s]{2,24}|[а-яА-ЯёЁ\s]{2,24})$/u", $name) ) {
            $sql = 'UPDATE `'.$this->config['db.char'].'`.`guild`
                    SET `name` = :name
                    WHERE `guildid` = :guild';
            $stmt = $this->db['char']->prepare($sql);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':guild', $guild);
            $stmt->execute();
        }
    }
}
?>