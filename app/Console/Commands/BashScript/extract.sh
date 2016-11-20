#!/bin/bash

export PGPASSWORD=$6
mkdir $5/$9
mkdir $5/${10}
mkdir $5/$7
IFS=$'\n'
for con in $(echo "$(psql -X -d $4 -U $3 -h $1 -p $2 -t -c "SELECT id,metadata->>'open_contracting_id' from
contracts limit 10")"|xargs -n3)
  do
    contractid=$(echo $con|awk '{print $1}')
    ocid=$(echo $con|awk '{print $3}')
    psql -X -d $4 -U $3 -h $1 -p $2 -t -A -F"," -c "select text from contract_pages where contract_id=${contractid}"
    > $5/$9/${ocid}.txt
    #sed 's/<br \/>//g' $5/$9/${ocid}.txt > $5/${10}/${ocid}.txt
done

#localhost
#5432
#postgres
#nrgi_new
#/Users/dragonSlayer/yipl/nrgi/storage
#alltext
#contract_text_2016_11_18
#rawtext
#refinedtext

cd $5/$9
zip -r $5/$7/$7'.zip' ./*

cd /
cd $5/$9
cd /
cd $5/$7

FILE_NAME=$7'.zip'
FILE_SIZE=$(wc -c "$FILE_NAME" | awk '{print $1}')
echo "Size of $FILE_NAME = $FILE_SIZE bytes."

FINAL_FILE_NAME=$7-${FILE_SIZE}'.zip'
mv $FILE_NAME $FINAL_FILE_NAME

cd /
cd $5
rm -rf $9





