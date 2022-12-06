<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("UI-компоненты");
?>
    <h2>Базовые компоненты</h2>
    <div class="mt30">
        В Миникорп используется компонентный подход. Таким образом, любой компонент можно использовать в любом месте и на любых сраницах сайта.<br>
        Любые классы можно чередовать, получая нужные вариации шаблона.
    </div>
    <hr>
    <h3>Цвет текста</h3>
    <div class="mt30">
        <div class="row row-in">
            <div class="col-xs-12 col-md-4 mt30">
                <h4 class="mb30">
                    Основной цвет темы
                </h4>
                <input type="text" value='<p class="color-primary">...</p>'>
                <p class="color-primary">
                    Текст основного цвета
                </p>
            </div>
            <div class="col-xs-12 col-md-4 mt30">
                <h4 class="mb30">
                    Второстепенный цвет
                </h4>
                <input type="text" value='<p class="color-secondary">...</p>'>
                <p class="color-secondary">
                    Текст второстепенного цвета
                </p>
            </div>
            <div class="col-xs-12 col-md-4 mt30" style="background-color: #555555">
                <h4 class="mb30 color-light">
                    Белый текст
                </h4>
                <input class="input_light" type="text" value='<p class="color-light">...</p>'>
                <p class="color-light">
                    Текст белого цвета
                </p>
            </div>
        </div>
    </div>
    <h3>Кнопки</h3>
    <div class="mt30">
        <div class="row row-in">
            <div class="col-xs-12 col-md-4 mt30">
                <h4 class="mb30">
                    Важная кнопка
                </h4>
                <input type="text" value='<a href="#" class="btn btn-primary">Сделать заказ</a>'>
                <div class="mt30">
                    <a href="#" class="btn btn-primary">Сделать заказ</a>
                </div>

            </div>
            <div class="col-xs-12 col-md-4 mt30">
                <h4 class="mb30">
                    Второстепенная кнопка
                </h4>
                <input type="text" value='<a href="#" class="btn btn-default">Сделать заказ</a>'>
                <div class="mt30">
                    <a href="#" class="btn btn-default">Сделать заказ</a>
                </div>
            </div>
            <div class="col-xs-12 col-md-4 mt30">
                <h4 class="mb30">
                    Кнопка с обводкой
                </h4>
                <input type="text" value='<a href="#" class="btn">Сделать заказ</a>'>
                <div class="mt30">
                    <a href="#" class="btn">Сделать заказ</a>
                </div>
            </div>
            <div class="col-xs-12 col-md-4 mt30">
                <h4 class="mb30">
                    Большая кнопка
                </h4>
                <input type="text" value='<a href="#" class="btn btn-primary btn-xl">Сделать заказ</a>'>
                <div class="mt30">
                    <a href="#" class="btn btn-primary btn-xl">Сделать заказ</a>
                </div>

            </div>
            <div class="col-xs-12 col-md-4 mt30">
                <h4 class="mb30">
                    Маленькая кнопка
                </h4>
                <input type="text" value='<a href="#" class="btn btn-default btn-xs">Сделать заказ</a>'>
                <div class="mt30">
                    <a href="#" class="btn btn-default btn-xs">Сделать заказ</a>
                </div>
            </div>
            <div class="col-xs-12 col-md-4 mt30">
                <h4 class="mb30">
                    Кнопка на всю ширину
                </h4>
                <input type="text" value='<a href="#" class="btn btn-full-width">Сделать заказ</a>'>
                <div class="mt30">
                    <a href="#" class="btn btn-full-width">Сделать заказ</a>
                </div>
            </div>
        </div>
    </div>
    <h3 class="mt80">Отступы</h3>
    <div class="mt30">
        Сделать отступ очень просто. Достаточно указать буквенное обозначение и добавить параметр от 0 до 80
        <div class="row row-in">
            <div class="col-xs-12 col-md-6 mt30">
                <h4 class="mb30">
                    Внешние отступы
                </h4>
                <ul>
                    <li><b>mt20</b> - соответствует margin-top: 20px</li>
                    <li><b>mb30</b> - соответствует margin-bottom: 30px</li>
                    <li><b>ml20</b> - соответствует margin-left: 20px</li>
                    <li><b>mr20</b> - соответствует margin-right: 20px</li>
                </ul>
            </div>
            <div class="col-xs-12 col-md-6 mt30">
                <h4 class="mb30">
                    Внутренние отступы
                </h4>
                <ul>
                    <li><b>pt20</b> - соответствует padding-top: 20px</li>
                    <li><b>pb20</b> - соответствует padding-bottom: 20px</li>
                    <li><b>pl60</b> - соответствует padding-left: 60px</li>
                    <li><b>pr40</b> - соответствует padding-right: 40px</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row row-in">
        <div class="col-md-12">
            <hr>
            <h3 class="mb0 mt80">Формы</h3>
        </div>
        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Текстовое поле</h4>
            <input type="text" value='<input type="text" value="Иванов Иван">'>

            <div class="mt30 pt20 pl20 pb20 pr20" style="background-color: #555;">
                <h4 class="mt0 color-light">Текстовое поле светлое</h4>
                <input class="input_light" type="text" value='<input class="input_light" type="text" value="Иванов Иван">'>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Текстовое поле с плавающим лейблом</h4>
            <textarea name="" id="" cols="3" rows="3">
<div class="fluid-label">
    <input id="form-name"  class="form-control" type="text" name="name" placeholder="Ваше имя*" />
    <label for="form-name">Ваше имя <span class="required-star">*</span></label>
    <i class="fa fa-user-o"></i>
</div>
        </textarea>
            <div class="mt30">
                <div class="fluid-label">
                    <input id="form-name"  class="form-control" type="text" name="name" placeholder="Ваше имя*" />
                    <label for="form-name">Ваше имя <span class="required-star">*</span></label>
                    <i class="fa fa-user-o"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-in">
        <div class="col-md-12">
            <hr>
            <h3 class="mb0 mt80">Важный текст</h3>

        </div>
        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Важный текст</h4>
            <input type="text" value='<div class="intro"></div>'>
            <div class="intro mt30 ">
                Наш сервис-центр проводит профессиональный ремонт MacBook уже не первый год, мы обслуживаем всю линейку ноутбуков Apple.
            </div>
        </div>
        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Важный текст + тонкое начертание</h4>
            <input type="text" value='<div class="intro font-md_light"></div>'>
            <div class="intro font-md_light mt30 ">
                Наш сервис-центр проводит профессиональный ремонт MacBook уже не первый год, мы обслуживаем всю линейку ноутбуков Apple.
            </div>
        </div>
        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Важный текст большого размера</h4>
            <input type="text" value='<div class="intro font-md"></div>'>
            <div class="intro font-md mt30 ">
                Наш сервис-центр проводит профессиональный ремонт MacBook уже не первый год, мы обслуживаем всю линейку ноутбуков Apple.
            </div>
        </div>
        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Важный текст большого размера + тонкое начертание</h4>
            <input type="text" value='<div class="intro font-md font-md_light"></div>'>
            <div class="intro font-md font-md_light mt30 ">
                Наш сервис-центр проводит профессиональный ремонт MacBook уже не первый год, мы обслуживаем всю линейку ноутбуков Apple.
            </div>
        </div>
    </div>

    <div class="row row-in">
        <div class="col-md-12">
            <hr>
            <h3 class="mb0 mt80">Списки ul li</h3>
        </div>
        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Обычный список</h4>
            <textarea name="" id="" cols="1" rows="3">
<ul>
    <li>механические повреждения корпуса Макбука;
        <ul>
            <li>неисправность кнопки включения</li>
            <li>Залитие клавиатуры</li>
        </ul>
    </li>
    <li>проблемы с программным обеспечением;</li>
    <li>неисправность графической системы;</li>
</ul>
        </textarea>
            <h5 class="mt30 и">
                Результат
            </h5>
            <ul>
                <li>механические повреждения корпуса Макбука;
                    <ul>
                        <li>неисправность кнопки включения</li>
                        <li>Залитие клавиатуры</li>
                    </ul>
                </li>
                <li>проблемы с программным обеспечением;</li>
                <li>неисправность графической системы;</li>
                <li>выход из строя матрицы устройства;</li>
                <li>перезагрузки и самопроизвольные выключения ноутбука.</li>
            </ul>
        </div>

        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Обычный список</h4>
            <textarea name="" id="" cols="1" rows="3">
<ol>
    <li>механические повреждения корпуса Макбука;
        <ol>
            <li>неисправность кнопки включения</li>
            <li>Залитие клавиатуры</li>
        </ol>
    </li>
    <li>проблемы с программным обеспечением;</li>
    <li>неисправность графической системы;</li>
</ol>
        </textarea>
            <h5 class="mt30 и">
                Нумерованый список
            </h5>
            <ol>
                <li>механические повреждения корпуса Макбука;
                    <ol>
                        <li>неисправность кнопки включения</li>
                        <li>Залитие клавиатуры</li>
                    </ol>
                </li>
                <li>проблемы с программным обеспечением;</li>
                <li>неисправность графической системы;</li>
                <li>выход из строя матрицы устройства;</li>
                <li>перезагрузки и самопроизвольные выключения ноутбука.</li>
            </ol>
        </div>
    </div>

    <div class="row row-in">
        <div class="col-md-12">
            <hr>
            <h3 class="mb0 mt80">История и даты</h3>
        </div>

        <div class="col-xs-12 col-md-6 mt30">
            <h5 class="mt30">
                Код
            </h5>
            <textarea name="" id="" cols="1" rows="8">
<div class="history">
    <div class="history-item">
        <div class="history-item--year">
            2014 г
        </div>
        <div class="history-item--text">
            Устроился в нашу компанию на должности помощника заместителя генерального директора
        </div>
    </div>
    <div class="history-item">
        ...
    </div>
</div>
        </textarea>
        </div>
        <div class="col-xs-12 col-md-6 mt30">
            <h5 class="mt30">
                Результат
            </h5>
            <div class="history py-3">
                <div class="history-item">
                    <div class="history-item--year">
                        2014 г
                    </div>
                    <div class="history-item--text">
                        Устроился в нашу компанию на должности помощника заместителя генерального директора
                    </div>
                </div>
                <div class="history-item">
                    <div class="history-item--year">
                        2016 г
                    </div>
                    <div class="history-item--text">
                        За отличные успехи в течение 2015 года получил повышение до должности заместителя генерального директора по экономическому планированию и стратегии.
                    </div>
                </div>
                <div class="history-item">
                    <div class="history-item--year">
                        Сегодня
                    </div>
                    <div class="history-item--text">
                        Работает в должности директора по экономическому планированию.
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row row-in">
        <div class="col-md-12">
            <hr>
            <h3 class="mb0 mt80">Таблицы</h3>

        </div>
        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Стандартная таблица</h4>
            <textarea name="" id="" cols="1" rows="3">
<div class="table-responsiver">
<table>
    <thead>
        <tr>
            <th>Вид работ</th>
            <th>Стоимость</th>
            <th>Время<em>*</em></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <span> Замена LCD в сборе с крышкой</span>
            </td>
            <td>
                <span>
                от 34900
                </span>
            </td>
            <td>
                <span>3 дня</span>
            </td>
        </tr>
    </tbody>
</table>
</div>
        </textarea>

            <div class="table-responsiver">
                <table class="mt30">
                    <thead>
                    <tr>
                        <th>Вид работ</th>
                        <th>Стоимость</th>
                        <th>Время<em>*</em></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <span> Замена LCD в сборе с крышкой</span>
                        </td>
                        <td>
                                    <span>
                                    от 34900
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>4 дня</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span> Замена клавиатуры MacBook 12"</span>
                        </td>
                        <td>
                                    <span>
                                    11900
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>4 часа</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span> Чистка после попадания влаги</span>
                        </td>
                        <td>
                                    <span>
                                    3000
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>от 1 дня</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Таблица с светлым заголовком</h4>
            <textarea name="" id="" cols="1" rows="3">
<div class="table-responsiver">
    <table class="table-light">
        <thead>
            <tr>
                <th>Вид работ</th>
                <th>Стоимость</th>
                <th>Время<em>*</em></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span> Замена LCD в сборе с крышкой</span>
                </td>
                <td>
                    <span>
                    от 34900
                    </span>
                </td>
                <td>
                    <span>3 дня</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
        </textarea>

            <div class="table-responsiver">
                <table class="table-light mt30">
                    <thead>
                    <tr>
                        <th>Вид работ</th>
                        <th>Стоимость</th>
                        <th>Время<em>*</em></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <span> Замена LCD в сборе с крышкой</span>
                        </td>
                        <td>
                                    <span>
                                    от 34900
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>4 дня</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span> Замена клавиатуры MacBook 12"</span>
                        </td>
                        <td>
                                    <span>
                                    11900
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>4 часа</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span> Чистка после попадания влаги</span>
                        </td>
                        <td>
                                    <span>
                                    3000
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>от 1 дня</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Стандартная таблица с рамками</h4>
            <textarea name="" id="" cols="1" rows="3">
<div class="table-responsiver">
    <table class="table-bordered">
        <thead>
            <tr>
                <th>Вид работ</th>
                <th>Стоимость</th>
                <th>Время<em>*</em></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span> Замена LCD в сборе с крышкой</span>
                </td>
                <td>
                    <span>
                    от 34900
                    </span>
                </td>
                <td>
                    <span>3 дня</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
        </textarea>

            <div class="table-responsiver">
                <table class="table-bordered mt30">
                    <thead>
                    <tr>
                        <th>Вид работ</th>
                        <th>Стоимость</th>
                        <th>Время<em>*</em></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <span> Замена LCD в сборе с крышкой</span>
                        </td>
                        <td>
                                    <span>
                                    от 34900
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>4 дня</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span> Замена клавиатуры MacBook 12"</span>
                        </td>
                        <td>
                                    <span>
                                    11900
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>4 часа</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span> Чистка после попадания влаги</span>
                        </td>
                        <td>
                                    <span>
                                    3000
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>от 1 дня</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 mt30">
            <h4 class="mt0">Таблица без рамок</h4>
            <textarea name="" id="" cols="1" rows="3">
<div class="table-responsiver">
    <table class="table-no-border">
        <thead>
            <tr>
                <th>Вид работ</th>
                <th>Стоимость</th>
                <th>Время<em>*</em></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span> Замена LCD в сборе с крышкой</span>
                </td>
                <td>
                    <span>
                    от 34900
                    </span>
                </td>
                <td>
                    <span>3 дня</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
        </textarea>

            <div class="table-responsiver">
                <table class="table-no-border mt30">
                    <thead>
                    <tr>
                        <th>Вид работ</th>
                        <th>Стоимость</th>
                        <th>Время<em>*</em></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <span> Замена LCD в сборе с крышкой</span>
                        </td>
                        <td>
                                    <span>
                                    от 34900
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>4 дня</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span> Замена клавиатуры MacBook 12"</span>
                        </td>
                        <td>
                                    <span>
                                    11900
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>4 часа</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span> Чистка после попадания влаги</span>
                        </td>
                        <td>
                                    <span>
                                    3000
                                        <span class="rur">Р</span>
                                    </span>
                        </td>
                        <td>
                            <span>от 1 дня</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="row row-in">
        <div class="col-md-12">
            <hr>
            <h3 class="mb0 mt80">Файлы</h3>

        </div>
        <div class="col-xs-12 col-md-12 mt30">
            Для вставки файлов на страницу, используйте код ниже и следующие классы:
            file_pdf, file_png, file_jpg, file_xls, file_doc, file_ppt, file_zip, file_tif
            <textarea name="" id="" cols="1" rows="3">
<a href="#" class="file file_png">
    <span class="file--name">Файл формата png</span>
    <span class="file--size">48.6 Кб</span>
</a>
        </textarea>
            <div class="row mt50">
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <a href="#" class="file file_png">
                        <span class="file--name">Файл формата png</span>
                        <span class="file--size">48.6 Кб</span>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <a href="#" class="file file_pdf">
                        <span class="file--name">Файл формата pdf</span>
                        <span class="file--size">48.6 Кб</span>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <a href="#" class="file file_doc">
                        <span class="file--name">Файл формата DOC</span>
                        <span class="file--size">48.6 Кб</span>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <a href="#" class="file file_xls">
                        <span class="file--name">Файл формата XLS</span>
                        <span class="file--size">48.6 Кб</span>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <a href="#" class="file file_ptt">
                        <span class="file--name">Файл формата PowerPoint</span>
                        <span class="file--size">48.6 Кб</span>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <a href="#" class="file file_jpg">
                        <span class="file--name">Файл формата JPG</span>
                        <span class="file--size">48.6 Кб</span>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <a href="#" class="file file_tif">
                        <span class="file--name">Файл формата TIF</span>
                        <span class="file--size">48.6 Кб</span>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <a href="#" class="file file_zip">
                        <span class="file--name">Файл формата ZIP</span>
                        <span class="file--size">48.6 Кб</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>