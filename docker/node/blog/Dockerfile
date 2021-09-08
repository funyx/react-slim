# pull official base image
FROM node:alpine3.14 AS development

# set working directory
WORKDIR /app

# add `/app/node_modules/.bin` to $PATH
ENV PATH /app/node_modules/.bin:$PATH

# install app dependencies
COPY ./react/blog/package.json ./

RUN yarn install

# add app
COPY ./react/blog ./
# Expose port
EXPOSE ${APP_BLOG_PORT}
# start app
CMD ["yarn", "start"]
