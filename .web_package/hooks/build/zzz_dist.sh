(rm dist || mkdir -p dist && rsync -av src/ dist/src/ && rsync -av vendor/ dist/vendor/ && cp slim_time.info dist/ && cp composer.json dist/ && cp jquery.slim_time.min.js dist/)