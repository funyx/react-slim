# pull official base image
FROM node:alpine3.14 AS development

# set working directory
WORKDIR /app

# add `/app/node_modules/.bin` to $PATH
ENV PATH /app/node_modules/.bin:$PATH

# install app dependencies
COPY ./react/admin/package.json ./

RUN yarn install

# add app
COPY ./react/admin ./
# Expose port
EXPOSE ${APP_ADMIN_PORT}
# start app
CMD ["yarn", "start"]
