FROM php:7.2-cli  

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY ./scripts /usr/src/scripts
COPY ./data /usr/src/scripts/data
WORKDIR /usr/src/scripts
RUN chmod +x runtests.sh

CMD [ "./runtests.sh" ]
