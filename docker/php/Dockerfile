ARG BASE_IMAGE=ghcr.io/sylius/sylius-php:8.3-alpine

FROM surnet/alpine-wkhtmltopdf:3.23.4-0.12.6-full AS wkhtmltopdf

FROM ${BASE_IMAGE}

USER root

RUN apk add --no-cache \
        libstdc++ \
        libx11 \
        libxrender \
        libxext \
        fontconfig \
        freetype \
        ttf-dejavu

COPY --from=wkhtmltopdf /bin/wkhtmltopdf /usr/local/bin/wkhtmltopdf
COPY --from=wkhtmltopdf /bin/wkhtmltoimage /usr/local/bin/wkhtmltoimage
COPY --from=wkhtmltopdf /lib/libwkhtmltox* /usr/local/lib/
