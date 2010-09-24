mkdir release
cp -R administrator release/
cp -R component release/
cp *.php release/
cp *.xml release/
tar -C release/ -zcf release.tar.gz .
rm -rf release
