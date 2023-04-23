<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Итератор HTML-файлов.
 *
 * @author Konstantin Antipin
 */

class HTMLIterator implements Iterator
{
    /**
     * Счётчик строк.
     *
     * @var int
     */
    private $rowCounter = null;

    /**
     * Указатель на HTML-файл.
     *
     * @var resource
     */
    private $filePointer = null;

    /**
     * Текущий элемент, который возвращается на каждой итерации.
     *
     * @var string
     */
    private $currentElement = null;

    /**
     * Конструктор пытается открыть HTML-файл. Он выдаёт исключение при ошибке.
     *
     * @param string $file HTML-файл.
     *
     * @throws \Exception
     */
    public function __construct($file)
    {
        try {
            $this->filePointer = fopen($file, 'r');
        } catch (Exception $e) {
            throw new Exception('The file "' . $file . '" cannot be read.');
        }
    }

    /**
     * Этот метод сбрасывает указатель файла.
     */
    public function rewind(): void
    {
        $this->rowCounter = 0;
        rewind($this->filePointer);
    }

    /**
     * Этот метод возвращает текущую HTML-строку.
     *
     * @return array Текущая HTML-строка.
     */
    public function current(): mixed
    {
        $this->currentElement = fgets($this->filePointer);
        return $this->currentElement;
    }

    /**
     * Этот метод возвращает номер текущей строки.
     *
     * @return int Номер текущей строки.
     */
    public function key(): int
    {
        return $this->currentElement;
    }

    /**
     * Этот метод переводит указатель на следующую строку.
     */
    public function next(): void
    {
        $this->rowCounter++;
    }

    /**
     * Этот метод проверяет, достигнут ли конец файла.
     *
     * @return bool
     */
    public function valid(): bool
    {
        if (feof($this->filePointer)) {
            fclose($this->filePointer);
            return false;
        }
        return true;
    }
}

$html = new HTMLIterator('task.html');

$fh = fopen('new.html', 'c');
foreach ($html as $string) {
    if (str_contains($string, '<title>') || str_contains($string, '<meta name="description"') || str_contains($string, '<meta name="keywords"')) continue;
    fseek($fh, 0, SEEK_END);
    fwrite($fh, $string);
}
fclose($fh);

// echo file_get_contents('new.html');