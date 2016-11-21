#!/bin/bash
#Parameter Definition
#Value - Example                                        Parameter Number
#localhost                                                      1
#5432                                                           2
#postgres                                                       3
#nrgi_new                                                       4
#/Users/dragonSlayer/yipl/nrgi/storage                          5
#categorytext                                                   6
#2016_11_21                                                     7
#rawtext                                                        8
#refinedtext                                                    9
#TN                                                             10
export PGPASSWORD=$6
mkdir $5/$9
IFS=$'\n'
for con in $(echo "$(psql -X -d $4 -U $3 -h $1 -p $2 -t -c "SELECT id,metadata->>'open_contracting_id' from contracts where metadata->'country'->>'code'='${10}'")"|xargs -n3)
  do
    contractid=$(echo $con|awk '{print $1}')
    ocid=$(echo $con|awk '{print $3}')
    psql -X -d $4 -U $3 -h $1 -p $2 -t -A -F"," -c "select text from contract_pages where contract_id=${contractid}" > $5/$9/${ocid}.txt
done

cd $5/$9
zip -r $5/'download'/${10}-$7'.zip' ./*

cd /
cd $5/'download'

FILE_NAME=${10}-$7'.zip'
FILE_SIZE=$(wc -c "$FILE_NAME" | awk '{print $1}')
echo "Size of $FILE_NAME = $FILE_SIZE bytes."

FINAL_FILE_NAME=${10}-$7-${FILE_SIZE}'.zip'
mv $FILE_NAME $FINAL_FILE_NAME

cd /
cd $5
rm -rf $9