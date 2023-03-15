Pure PHP 8+, pure ES14, pure CSS3+

Config see in `config-sample.inc`, edit and save as `config.inc`

Web usage: https://word-art.boris-tim.site

Parsing usage: `php index.php -k=CRON_KEY` use CRON_KEY constant from `config.php`

Requires: PHP8+, ECMA7+, CSS3+    
Depends: mysqli, curl, zlib, memcache

==============================================

Написать скрипт, собирающий данные с рейтинга Word Art (http://www.world-art.ru/cinema/), в группах (Рейтинг полнометражных фильмов / западных сериалов / японских дорам / корейских дорам / российских сериалов) и сохраняющего позицию, расчетный балл, голоса, средний балл, название фильма, год и краткое содержание в БД (MySQL, или PostgreSQL). Дополнительно к каждому фильму нужно хранить обложку. Также необходимо добавить соответствующие поля в БД для выборки рейтинга на определенную дату. Скрипт должен быть написан с учетом возможности постановки в cron (скрипт должен собирать данные за конкретную дату, или период дат).

Создать базовую веб-страницу, выводящую топ-10 фильмов на указанную дату, по группам (должны отображаться колонки: позиция в рейтинге, обложка, название фильма (можно тайтлом по обложке), расчетный балл, голоса, средний балл, год). Должны быть возможность отсортировать данные по всем указанным в выборке колонкам. Должно присутствовать поле, где пользователь может указать дату выборки. При нажатии на название фильма, должно открываться модальное окно с подробной информацией о фильме (к полям из таблицы должно добавится поле «краткое содержание»). При выгрузке данных из СУБД должен быть использован кэширующий слой, чтобы избежать запросов к базе, каждый раз, когда рейтинг должен быть показан.
