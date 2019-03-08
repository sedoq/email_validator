<?php

namespace img\mail;

require_once __DIR__ . '/Messages.php';

use img\mail\Messages;

/**
 * Class XML
 * Реализует базовые методы для генерации xml-документов
 * @package img\mail
 */
class XML
{
    /**
     * Корневной элемент выгрузки
     * @access public
     * @var string
     */
    public $rootElement = 'root';

    /**
     * Переменная, содержащая данные выгрузки в xml
     * @access protected
     * @var null
     */
    protected $_xml = null;


    /**
     * XML constructor.
     * Устанавливает заголовок xml-документа, базовую структуру xml-документа
     */
    public function __construct ()
    {
        $this->setHeader();
        $this->startDocument();
        $this->startElement($this->rootElement);
    }

    /**
     * Завершает xml-документ
     * Очищает xml-буфер
     */
    public function __destruct ()
    {
        $this->endElement();
        $this->flushXML();
        $this->endDocument();
        unset($this->_xml);
    }

    /**
     * Определяет начало xml-элемента, отображает комментарий (в режиме разработки)
     * @access protected
     * @param $element string элемент, начало которого определяет текущий метод
     */
    public function startElement($element)
    {
        $this->_xml->startElement($element);
    }

    /**
     * Записывает значение аттрибута xml
     * @access public
     * @param $element
     * @param $value
     */
    public function writeElement($element, $value)
    {
        $this->_xml->writeElement($element, $value);
    }

    /**
     * @access protected
     * @param $comment string текст комментария
     * @return null|mixed устанавливает комментарий в режиме разработки, иначе - null
     */
    public function comment($comment)
    {
        return $this->_xml->writeComment($comment);
    }

    /**
     * Определяет завершение xml-аттрибута
     * @access protected
     */
    public function endElement()
    {
        $this->_xml->endElement();
    }

    /**
     * Устанавливает заголовок тип содержимого документа
     * @access protected
     * @return mixed
     */
    protected function setHeader()
    {
        return header("Content-type: text/xml");
    }

    /**
     * Определяет начало xml-документа, буфер вывода, кодировку
     * @access protected
     */
    protected function startDocument()
    {
        $this->_xml = new \XMLWriter();
        $this->_xml->openURI('php://output');
        $this->_xml->startDocument('1.0', 'UTF-8');
        $this->comment(Messages::get('ROOT'));
    }

    /**
     * Определяет завершение xml-документа
     * @access protected
     */
    protected function endDocument()
    {
        $this->_xml->endDocument();
    }

    /**
     * Очищает xml-буфер
     * @access protected
     * @return mixed
     */
    protected function flushXML()
    {
        return $this->_xml->flush();
    }
}