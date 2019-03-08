<?php

namespace img\mail;

/**
 * Class Message
 * Возвращает запрошенный текст по коду
 * @package img\mail
 */
class Messages
{
    /**
     * Источник сообщений
     * @var mixed
     */
    protected $_data;

    /**
     * Message constructor.
     * Осуществляет загрузку списка сообщений в переменную
     * @throws \Exception бросает исключение, если файл не существует
     */
    public function __construct ()
    {
        try{
            $this->_data = include(__DIR__ . "/../message/source.php");
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage(); exit();
        }
    }

    /**
     * Возвращает текст по ключу
     * @access public
     * @param $name string код, возвращаемого сообщения
     * @return mixed
     */
    public static function get($name)
    {
        $messages = new self();
        if (isset($messages->_data[$name]) && !empty($messages->_data[$name])) {
            return $messages->_data[$name];
        }
        return $name;
    }
}