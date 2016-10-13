php-pageregions
===============

Показ на страницах
------------------
### URL
Одна строка - один фильтр. Каждый фильтр должен начинаться с символов **+** или **-** и потом пробела.
После символа **+** или **-** и пробела указывается маска адреса. 

    + включает показ блока на этих адресах
    - выключает

Вот пример фильтра для болка, который показывается на всех страницах CS:GO, кроме Dreamhack.

    + csgo
    - csgo/dreamhack

Маска - это регулярное выражение.

    csgo/dreamhack - может входить в адрес в любом месте
    ^/csgo/dreamhack - значит должно входить именно в начале адреса
    ^/csgo/dreamhack$ - значит что в адресе больше ничего не должно быть

* Адреса начинаются со **/**
* Главная страница - это **^/$**
* Может использоваться вмести с PageType

### PageType
Одна строка - один фильтр. Каждый фильтр должен начинаться с символов **+** или **-** и потом пробела.
После символа **+** или **-** и пробела указывается маска адреса.

    + включает показ блока на этих типах страниц
    - выключает


Вот пример фильтра для болка, который показывается на всех типах страниц Article, кроме Main.

    + ^Article$
    - ^Main$

* Страинца с типов Article - **^Article$**
* Может использоваться вмести с URL



