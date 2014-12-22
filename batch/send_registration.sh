#/bin/sh
set -x
RuntimePrint()
{
 duration=$(echo "scale=3;(${m2t}-${m1t})/(1*10^09)"|bc|sed 's/^\./0./')
 echo -e "DEBUG: lines ${m1l}-${m2l}\t${duration}\tsec" >> $LOG
}
executa_link()
{		
	curl -L -k -i $URL > $TEMP_FILE1
	if [ $? -eq 0 ]
	then
		echo "============================="  >> $LOG
	else
		echo "erro no curl" >> $LOG
	fi
	if [ $(cat $TEMP_FILE1 | grep -c "HTTP/1.1 204 No Content") -gt 0 ]; then
		echo "terminado o job" >> $LOG
		m2t=$(date +%s%N); m2l=$LINENO; RuntimePrint
		exit 0
	else
		if [ $(cat $TEMP_FILE1 | grep -c "HTTP/1.1 200") -gt 0 ]; then
			executa_link
		else
			echo "erro no job do job" >> $LOG
			m2t=$(date +%s%N); m2l=$LINENO; RuntimePrint
			exit 1
		fi
	fi
}
BASE_FILE=/var/www/html/PRVPR/htdocs/batch
TEMP_FILE1=$BASE_FILE/vpr.1.tmp.txt
LOG=$BASE_FILE/job.log
URL='http://vpr.localhost/app_dev.php/public/email/reminders/send'
m1t=$(date +%s%N); m1l=$LINENO
executa_link

