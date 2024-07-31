FROM nginx:latest

ARG APP_ENV
ENV USER_ID=1000 \
    GROUP_ID=1000 \
    USERNAME="nginx"

WORKDIR /etc/nginx

RUN apt-get update -qq --fix-missing && \
    apt-get install -qq -y --no-install-recommends \
    libcap2-bin && \
    touch /var/run/nginx.pid && \
    usermod -u $USER_ID -o nginx && groupmod -g $GROUP_ID -o nginx

COPY ./docker/nginx/config /etc/nginx/conf.d

RUN if [ "${APP_ENV}" != "dev" ]; then \
		rm -rf /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/prod.conf; \
        mv /etc/nginx/conf.d/dev.conf /etc/nginx/conf.d/default.conf; \
    else \
        rm -rf /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/prod.conf; \
        mv /etc/nginx/conf.d/dev.conf /etc/nginx/conf.d/default.conf; \
    fi

RUN chown --changes --silent --no-dereference --recursive ${USER_ID}:${GROUP_ID} \
        /var/run/nginx.pid \
        /var/log/nginx \
        /var/cache/nginx \
        /etc/nginx \
        /usr/lib/nginx 2>&1 >/dev/null && \
    setcap 'cap_net_bind_service=+ep' /usr/sbin/nginx
    
EXPOSE 80

STOPSIGNAL SIGTERM

USER ${USERNAME}

CMD ["nginx", "-g", "daemon off;"]
