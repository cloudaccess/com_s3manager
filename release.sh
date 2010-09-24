mkdir release
cp -R administrator release/
cp -R component release/
cp *.php release/
cp *.xml release/
find release/ -name .git -exec rm -rf {} \;
tar -C release/ -zcf com_s3manager_`date +"%Y-%m-%d"`.tar.gz .
rm -rf release
