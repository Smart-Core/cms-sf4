<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Tools;

class Breadcrumbs implements \Iterator, \Countable
{
    /**
     * @var int
     */
    protected $_position = 0;

    /**
     * @var array
     */
    protected $_breadcrumbs = [];

    public function rewind(): void
    {
        $this->_position = 0;
    }

    public function current()
    {
        return $this->_breadcrumbs[$this->_position];
    }

    public function key()
    {
        return $this->_position;
    }

    public function next(): self
    {
        ++$this->_position;

        return $this;
    }

    public function valid(): bool
    {
        return isset($this->_breadcrumbs[$this->_position]);
    }

    public function count(): int
    {
        return count($this->_breadcrumbs);
    }

    /**
     * Получить последний пункт.
     *
     * @return array|null
     */
    public function getLast(): ?array
    {
        return isset($this->_breadcrumbs[$this->count() - 1])
            ? $this->_breadcrumbs[$this->count() - 1]
            : null;
    }

    /**
     * Обновление последнего пункта.
     *
     * @param string $uri
     * @param string $title
     * @param bool $descr
     */
    public function updateLast($uri, $title, $descr = false)
    {
        if (isset($this->_breadcrumbs[$this->count() - 1])) {
            $this->_breadcrumbs[$this->count() - 1] = [
                'uri'   => $uri,
                'title' => $title,
                'descr' => $descr,
            ];
        }
    }

    /**
     * Установить данные.
     *
     * @param array $data
     */
    public function assign(array $data): void
    {
        $this->_breadcrumbs = $data;
    }

    /**
     * Добавление хлебной крошки.
     *
     * @param string $uri
     * @param string $title
     * @param string $descr
     */
    public function add(string $uri, string $title, $descr = false): Breadcrumbs
    {
        $this->_breadcrumbs[] = [
            'uri'   => $uri,
            'title' => $title,
            'descr' => $descr,
        ];

        return $this;
    }

    /**
     * Получиить хлебные крошки.
     *
     * @param int $num
     *
     * @return array
     */
    public function get(int $num = null): array
    {
        // @todo если $num отрицательный, то вернуть указанный номер с конца, например -1 это последний, а -2 предпослений и т.д...

        $data = [];
        $current_uri = '';
        foreach ($this->_breadcrumbs as $key => $value) {
            $data[$key] = $value;
            if ($this->isAbsolutePath($value['uri'])) {
                $current_uri = $value['uri'];
                continue;
            } else {
                $current_uri .= $value['uri'];
                $data[$key]['uri'] = $current_uri;
            }
        }

        return ($num === null) ? $data : $data[$num];
    }

    /**
     * Получить всю цепочку.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->get();
    }

    /**
     * Получение ссылки на последнюю крошку.
     *
     * @return string
     */
    public function getLastUri(): string
    {
        $item = $this->get(count($this->_breadcrumbs) - 1);

        return $item['uri'];
    }

    /**
     * Отрисовщик по умолчанию.
     */
    public function display(): void
    {
        $bc = $this->get();
        $cnt = count($bc);
        if ($cnt > 0) {
            foreach ($bc as $item) {
                echo --$cnt ? "<a href=\"{$item['uri']}\" title=\"{$item['descr']}\">" : '';
                echo $item['title'];
                echo $cnt ? '</a>&nbsp;&raquo;&nbsp;' : '';
            }
            echo "\n";
        }
    }

    /**
     * Является ли указанный путь абсолютным.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isAbsolutePath(string $path): bool
    {
        return (strpos($path, '/') === 0 or strpos($path, ':') === 1) ? true : false;
    }
}
