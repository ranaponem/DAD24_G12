#!/bin/bash

SESSION_NAME="vue-dev"

# Function to start the screen session
start() {
  if screen -list | grep -q "$SESSION_NAME"; then
    echo "The 'bun dev' process is already running in the screen session."
  else
    echo "Starting 'bun dev' in a new screen session..."
    screen -dmS "$SESSION_NAME" bash -c "bun dev; exec bash"
  fi
}

# Function to stop the screen session
stop() {
  if screen -list | grep -q "$SESSION_NAME"; then
    echo "Stopping the 'bun dev' screen session..."
    screen -S "$SESSION_NAME" -X quit
  else
    echo "No 'bun dev' process is running in the screen session."
  fi
}

# Function to restart the screen session (kill and then start it)
restart() {
  if screen -list | grep -q "$SESSION_NAME"; then
    echo "Restarting the 'bun dev' screen session..."
    screen -S "$SESSION_NAME" -X quit
    sleep 1 # Wait for the process to properly terminate
  else
    echo "No 'bun dev' process is running in the screen session."
  fi
  # Start a new session
  start
}

# Check the first argument passed to the script
case "$1" in
  start)
    start
    ;;
  stop)
    stop
    ;;
  restart)
    restart
    ;;
  *)
    echo "Usage: $0 {start|stop|restart}"
    exit 1
    ;;
esac

