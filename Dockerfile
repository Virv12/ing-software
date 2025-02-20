FROM php

RUN useradd -m phpuser

RUN apt-get update && apt-get install -y \
    msmtp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN echo "account default" > /etc/msmtprc \
    && echo "host mail" >> /etc/msmtprc \
    && echo "port 1025" >> /etc/msmtprc \
    && echo "from noreply@ing.unipi.it" >> /etc/msmtprc \
    && echo "tls_certcheck off" >> /etc/msmtprc \
    && chmod 600 /etc/msmtprc

RUN echo "logfile /var/log/msmtp.log" >> /etc/msmtprc \
    && touch /var/log/msmtp.log \
    && chown phpuser:phpuser /var/log/msmtp.log

RUN ln -s /usr/bin/msmtp /usr/sbin/sendmail

CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html"]
