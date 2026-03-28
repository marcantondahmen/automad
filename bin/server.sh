#!/bin/bash
PID_FILE=".php-server.pid"
LOG_FILE=".php-server.log"
URL="localhost:8000"

start() {
	if [ -f "$PID_FILE" ] && kill -0 $(cat "$PID_FILE") 2>/dev/null; then
		echo -e "  \033[0;32m Server already running\033[0m"
		return
	fi

	nohup php -S $URL >"$LOG_FILE" 2>&1 &
	echo $! >"$PID_FILE"
	echo -e "  \033[0;32m Started PHP server\033[0m\n"
	echo -e "  \033[0;34m Server output in:     \033[0;35m${LOG_FILE}\033[0m"
	echo -e "  \033[0;34m Site is running at:   \033[0;35mhttp://${URL}\033[0m"
}

stop() {
	if [ -f "$PID_FILE" ]; then
		kill $(cat "$PID_FILE") && rm "$PID_FILE"
		echo -e "  \033[0;32m Stopped PHP server\033[0m"
	else
		echo -e "  \033[0;31m No PID file found\033[0m"
	fi
}

case "$1" in
start)
	start
	;;
stop)
	stop
	;;
*)
	echo -e "\n  \033[1;30mUsage: \033[0;35m$0 {start|stop}\033[0;31m"
	;;
esac
