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

class CharactersModel extends Model {

    public $message;

    public function searchChar($char, $type) {
        if ( preg_match("/^[a-zA-Zа-яА-ЯёЁ0-9]+$/u", $char) && preg_match("/^[a-zA-Z]+$/u", $type) ) {
            $sql = "SELECT `guid`, `account`, `name`, `race`, `class`,
                            `gender`, `level`, `online`, `totaltime`,
                            `arenaPoints`, `totalHonorPoints`, `todayHonorPoints`,
                            `yesterdayHonorPoints`, `totalKills`, `todayKills`, `yesterdayKills`
                    FROM `{$this->config['db.char']}`.`characters`
                    WHERE `$type` = :char";
            $stmt = $this->db['char']->prepare($sql);
            $stmt->bindValue(':char', $char);
            $stmt->execute();
            $charInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $charInfo ) {
                include_once "system/modules/Arrays.php";
                $charInfo['faction'] = $faction[$charInfo['race']];
                $charInfo['race'] = $race[$charInfo['race']];
                $charInfo['class'] = $class[$charInfo['class']];
                $charInfo['gender'] = $gender[$charInfo['gender']];
                $charInfo['totaltime'] = $this->timeConvert($charInfo['totaltime']);
                $charInfo['online'] = $this->getOnlineChar($charInfo['online']);
                return  $charInfo;
            } else {
                $this->message = 'Персонаж с таким именем или id не найден';
            }
        } else {
            $this->message = 'Введены некорректные данные';
        }
    }

    public function changeCharName($char, $name, $type) {
        if ( preg_match("/^[a-zA-Zа-яА-ЯёЁ0-9]+$/u", $char) && preg_match("/^[a-zA-Zа-яА-ЯёЁ]+$/u", $name) && preg_match("/^[a-zA-Z]+$/u", $type) ) {
            $sql = "UPDATE `{$this->config['db.char']}`.`characters`
                    SET `name` = :name
                    WHERE `$type` = :char";
            $stmt = $this->db['char']->prepare($sql);

            // устанавливаем нужный регистр
            mb_internal_encoding("UTF-8");
            $name = mb_strtoupper(mb_substr($name,0,1)).mb_strtolower(mb_substr($name,1));

            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':char', $char);
            $stmt->execute();
            if ( $stmt->rowCount() == 1 ) {
                $this->message[1] = 'Смена имени прошла успешно';
            } else {
                $this->message[0] = 'Персонаж с таким именем или id не найден';
            }
        } else {
            $this->message[0] = 'Введены некорректные данные';
        }
    }

    public function changeCharRace($char, $race, $type) {
        if ( preg_match("/^[a-zA-Zа-яА-ЯёЁ0-9]+$/u", $char) && ($race > 0 && $race < 9) && preg_match("/^[a-zA-Z]+$/u", $type) ) {
            $sql = "UPDATE `{$this->config['db.char']}`.`characters`
                    SET `race` = :race
                    WHERE `$type` = :char";
            $stmt = $this->db['char']->prepare($sql);
            $stmt->bindValue(':race', (int)$race);
            $stmt->bindValue(':char', $char);
            $stmt->execute();
            if ( $stmt->rowCount() == 1 ) {
                $this->message[1] = 'Смена расы прошла успешно';
            } else {
                $this->message[0] = 'Персонаж с таким именем или id не найден';
            }
        } else {
            $this->message[0] = 'Введены некорректные данные';
        }
    }

    public function changeCharClass($char, $class, $type) {
        if ( preg_match("/^[a-zA-Zа-яА-ЯёЁ0-9]+$/u", $char) && ($class > 0 && $class < 12 && $class != 10) && preg_match("/^[a-zA-Z]+$/u", $type) ) {
            $sql = "UPDATE `{$this->config['db.char']}`.`characters`
                    SET `class` = :class
                    WHERE `$type` = :char";
            $stmt = $this->db['char']->prepare($sql);
            $stmt->bindValue(':class', (int)$class);
            $stmt->bindValue(':char', $char);
            $stmt->execute();
            if ( $stmt->rowCount() == 1 ) {
                $this->message[1] = 'Смена класса прошла успешно';
            } else {
                $this->message[0] = 'Персонаж с таким именем или id не найден';
            }
        } else {
            $this->message[0] = 'Введены некорректные данные';
        }
    }

    public function changeCharLevel($char, $level, $type) {
        if ( preg_match("/^[a-zA-Zа-яА-ЯёЁ0-9]+$/u", $char) && ($level > 0 && $level < 256) && preg_match("/^[a-zA-Z]+$/u", $type) ) {
            $sql = "UPDATE `{$this->config['db.char']}`.`characters`
                    SET `level` = :level
                    WHERE `$type` = :char";
            $stmt = $this->db['char']->prepare($sql);
            $stmt->bindValue(':level', (int)$level);
            $stmt->bindValue(':char', $char);
            $stmt->execute();
            if ( $stmt->rowCount() == 1 ) {
                $this->message[1] = 'Смена уровня прошла успешно';
            } else {
                $this->message[0] = 'Персонаж с таким именем или id не найден';
            }
        } else {
            $this->message[0] = 'Введены некорректные данные';
        }
    }

    public function getOnlineChar($online) {
        if ($online == 1)
            return "<font color=green>Online</font>";
        else
            return "<font color=red>Offline</font>";
    }
}
?>