教务系统用戶和權限
==============================

安裝步驟
1. 把整個目錄複製到 Apache 的 DocumentRoot
2. 設置Apache 的 DocumentRoot 路徑，例如教務系統的路徑為 /var/www/srs 的話, DocumentRoot 就應該設置成 /var/www/srs/public
3. 複製 app/config/database.php.dist 到 app/config/database.php
4. 修改 app/config/database.php 中的數據名稱，連接用戶名和密碼
3. 建立基本數據表和導入基本數據。在 srs 目錄下，執行:
    $ php artisan migrate
    $ php artisan db:seed
4. 更改目錄權限
    $ chmod 0755 -R /var/www/srs
    $ chown apache:apache -R /var/www/srs
5. 完成

登入系統
用戶名：admin
密碼：admin
用戶名：staff
密碼：staff
