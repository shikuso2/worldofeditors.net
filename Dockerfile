FROM ubuntu:22.04

# Update and install required dependencies
RUN apt update && apt install -y \
    git build-essential cmake imagemagick libmagickwand-dev sqlite3 libsqlite3-dev curl

WORKDIR /home/

# MPQExtractor
RUN git clone https://github.com/Ruk33/MPQExtractor.git

WORKDIR MPQExtractor

RUN git submodule init
RUN git submodule update
RUN cmake .
RUN cmake --build .
RUN mv bin/MPQExtractor /usr/local/bin/
RUN chmod +x /usr/local/bin/MPQExtractor

WORKDIR /home/

# BLPConverter
RUN git clone https://github.com/Ruk33/BLPConverter.git

WORKDIR BLPConverter

RUN cmake .
RUN make
RUN mv bin/BLPConverter /usr/local/bin/
RUN chmod +x /usr/local/bin/BLPConverter

WORKDIR /home/app

COPY ./ ./

# Install FrankenPHP
RUN curl https://frankenphp.dev/install.sh | sh && \
    mv frankenphp /usr/local/bin/ && \
    chmod +x /usr/local/bin/frankenphp

CMD ["frankenphp", "run"]
