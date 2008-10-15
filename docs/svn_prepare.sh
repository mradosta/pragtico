#No incluto los archivos core.php y databases.php
svn propset svn:ignore "core.php
database.php
svn-commit*" ../app/config

#Ignoro el directorio tmp
svn propset svn:ignore "*" ../app/tmp -R 

#Ignoro los archivos del proyecto quanta
svn propset svn:ignore "pragtico.webprj
pragtico.session" ../

#Ignoro los archivos de manuales propios del desarrollador
svn propset svn:ignore "*" ./developer_manuals

#Ignoro los archivos de data propios del desarrollador
svn propset svn:ignore "*" ./developer_data

#Agrego como repositorio externo a la rama 1.2 de cakePHP
svn propset svn:externals "cake https://svn.cakephp.org/repo/branches/1.2.x.x/cake" ../

#Le pongo los datos de cada revision dentro del archivo
svn propset svn:keywords "Revision LastChangedBy Date" ../app/controllers/*.php
svn propset svn:keywords "Revision LastChangedBy Date" ../app/models/*.php
