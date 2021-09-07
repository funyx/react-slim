FROM mysql:5.7.35
ENV MYSQL_RANDOM_ROOT_PASSWORD=1
COPY ./my.cfg /etc/mysql/conf.d/config-file.cnf
COPY setup.sql /docker-entrypoint-initdb.d/
