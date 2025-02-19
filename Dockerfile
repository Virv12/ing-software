# Use the official PHP image as the base image
FROM php

# Set a non-root user for security
RUN useradd -m phpuser

# Install necessary tools
RUN apt-get update && apt-get install -y \
    msmtp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure msmtp
#    && echo "tls on" >> /etc/msmtprc \
RUN echo "account default" > /etc/msmtprc \
    && echo "host mail" >> /etc/msmtprc \
    && echo "port 1025" >> /etc/msmtprc \
    && echo "from noreply@ing.unipi.it" >> /etc/msmtprc \
    && echo "tls_certcheck off" >> /etc/msmtprc \
    && chmod 600 /etc/msmtprc

# Set msmtp log for debugging (optional)
RUN echo "logfile /var/log/msmtp.log" >> /etc/msmtprc \
    && touch /var/log/msmtp.log \
    && chown phpuser:phpuser /var/log/msmtp.log

# Link msmtp to sendmail for PHP mail() function
RUN ln -s /usr/bin/msmtp /usr/sbin/sendmail

# Start the PHP container
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html"]
