# 軽量なAlpine Linuxをベースにした公式のPHP-FPMイメージを使用します
FROM php:8.4-fpm-alpine AS php

# MySQLに接続するためのPDOモジュールをインストールします
RUN docker-php-ext-install pdo_mysql

# 画像を保存するためのディレクトリを作成し、PHP実行ユーザー（www-data）に所有権とグループを設定します
RUN install -o www-data -g www-data -d /var/www/upload/image/

# PHPのファイルアップロード上限サイズおよびPOSTデータの最大サイズを5MBに変更し、設定ファイル(php.ini)に追記します
RUN echo -e "post_max_size = 5M\nupload_max_filesize = 5M" >> ${PHP_INI_DIR}/php.ini
