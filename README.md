## **PAKETİ KULLANMAK İÇİN GEREKLİ BİLGİLER**

SITE_URL= PROJE_URL

CLIENT_ID= CLIENT_ID

LDAP_URL= LDAP_URL TEST & PROD

LDAP_ROLE= LDAP ROLE

## **USER MODEL EKLENECEKLER**

use BulutKuru\IbbLdap\Traits\ExtractsFieldsFromTable; // Trait'inizi import edin
use ExtractsFieldsFromTable; // Trait'i kullanın

## **PAKET İÇİN CALISTIRILCAK KOMUTLAR**

php artisan ibbldap:install
