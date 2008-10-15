#No incluto los archivos core.php y databases.php
svn propset svn:ignore "core.php
database.php
svn-commit*" ../app/config

#Ignoro el directorio tmp
svn propset svn:ignore "*" ../app/tmp -R 

#Ignoro los archivos del proyecto quanta
svn propset svn:ignore "pragtico.webprj
pragtico.session" ../

#Agrego como repositorio externo a la rama 1.2 de cakePHP
svn propset svn:externals "cake https://svn.cakephp.org/repo/branches/1.2.x.x/cake" ../
#svn propget svn:externals cake
#../cake https://svn.cakephp.org/repo/branches/1.2.x.x/cake
