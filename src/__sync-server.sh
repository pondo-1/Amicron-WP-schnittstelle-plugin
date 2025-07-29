#!/bin/bash

# Pattern for files to be copied
FILE_PATTERN="*.php"

# Target directory on the server (must be passed as parameter)
REMOTE_DIR=""

# User and server (must be passed as parameter)
USER_SERVER=""

# Function to display help
show_help() {
  echo "Usage: $0 [Options] -s SERVER -r REMOTE_DIR"
  echo ""
  echo "Required Parameters:"
  echo "  -s, --server     Server address (Format: user@server)"
  echo "  -r, --remote-dir Remote directory on the server"
  echo ""
  echo "Options:"
  echo "  -u, --upload     Upload PHP files to the server"
  echo "  -d, --download   Download logfile.txt from the server"
  echo "  -e, --exports    Download exports folder from the server"
  echo "  -a, --all        Upload and download (default if no option specified)"
  echo "  -h, --help       Show this help"
  echo "  -v, --verbose    Verbose output (shows executed commands)"
  echo ""
  echo "Examples:"
  echo "  $0 -u -s user@example.com -r /path/to/remote/dir"
  echo "  $0 --upload --server ssh-user@myserver.com --remote-dir /var/www/html"
  echo "  $0 -a -s admin@server.de -r /home/web/project"
  echo ""
}

# Default values
DO_UPLOAD=false
DO_DOWNLOAD=false
DO_EXPORTS=false
VERBOSE=false

# Process parameters
if [ $# -eq 0 ]; then
  echo "Error: No parameters provided."
  show_help
  exit 1
fi

while [ "$1" != "" ]; do
  case $1 in
    -u | --upload )    DO_UPLOAD=true
                       ;;
    -d | --download )  DO_DOWNLOAD=true
                       ;;
    -e | --exports )   DO_EXPORTS=true
                       ;;
    -a | --all )       DO_UPLOAD=true
                       DO_DOWNLOAD=true
                       ;;
    -v | --verbose )   VERBOSE=true
                       ;;
    -s | --server )    shift
                       if [ -z "$1" ]; then
                         echo "Error: Server address expected after -s/--server."
                         show_help
                         exit 1
                       fi
                       USER_SERVER="$1"
                       ;;
    -r | --remote-dir ) shift
                       if [ -z "$1" ]; then
                         echo "Error: Remote directory expected after -r/--remote-dir."
                         show_help
                         exit 1
                       fi
                       REMOTE_DIR="$1"
                       ;;
    -h | --help )      show_help
                       exit 0
                       ;;
    * )                echo "Error: Unknown parameter '$1'"
                       show_help
                       exit 1
  esac
  shift
done

# Validate required parameters
if [ -z "$USER_SERVER" ]; then
  echo "Error: Server address (-s/--server) is required."
  show_help
  exit 1
fi

if [ -z "$REMOTE_DIR" ]; then
  echo "Error: Remote directory (-r/--remote-dir) is required."
  show_help
  exit 1
fi

# If no action was selected, default to upload and download
if [ "$DO_UPLOAD" = false ] && [ "$DO_DOWNLOAD" = false ] && [ "$DO_EXPORTS" = false ]; then
  DO_UPLOAD=true
  DO_DOWNLOAD=true
fi

# Display current configuration
if $VERBOSE; then
  echo "Configuration:"
  echo "  Server: $USER_SERVER"
  echo "  Remote Directory: $REMOTE_DIR"
  echo "  Upload: $DO_UPLOAD"
  echo "  Download: $DO_DOWNLOAD"
  echo "  Exports: $DO_EXPORTS"
  echo ""
fi

# Function to execute commands with optional display
execute_command() {
  local cmd="$1"

  if $VERBOSE; then
    echo "Executing: $cmd"
  fi

  eval $cmd
  return $?
}

# Upload files if requested
if $DO_UPLOAD; then
  echo "Uploading PHP files to the server..."

  if $VERBOSE; then
    echo "Executing: rsync -avz -e 'ssh -o StrictHostKeyChecking=no' --include='*.php' --include='*/' --exclude='*' ./ '$USER_SERVER:$REMOTE_DIR/'"
  fi

  rsync -avz -e "ssh -o StrictHostKeyChecking=no" --include="*.php" --include="*/" --exclude="*" ./ "$USER_SERVER:$REMOTE_DIR/"

  # Check if the command was successful
  if [ $? -eq 0 ]; then
    echo "The files were successfully copied."
  else
    echo "Error copying the files."
  fi
fi

# Download log file if requested
if $DO_DOWNLOAD; then
  echo "Downloading logfile.txt from the server..."

  if $VERBOSE; then
    echo "Executing: rsync -avz -e 'ssh -o StrictHostKeyChecking=no' '$USER_SERVER:$REMOTE_DIR/logfile.txt' '../logfiles/logfile.txt_echt_server'"
  fi

  rsync -avz -e "ssh -o StrictHostKeyChecking=no" "$USER_SERVER:$REMOTE_DIR/logfile.txt" "../logfiles/logfile.txt_echt_server"

  # Check if downloading the log file was successful
  if [ $? -eq 0 ]; then
    echo "The log file was successfully downloaded."
  else
    echo "Error downloading the log file."
  fi
fi

# Download exports folder if requested
if $DO_EXPORTS; then
  echo "Downloading exports folder from the server..."

  # Ensure the local exports folder exists
  mkdir -p "../exports"

  EXPORTS_CMD="rsync -avz -e 'ssh -o StrictHostKeyChecking=no' '$USER_SERVER:$REMOTE_DIR/exports/' '../exports/'"

  execute_command "$EXPORTS_CMD"

  # Check if downloading the exports folder was successful
  if [ $? -eq 0 ]; then
    echo "The exports folder was successfully downloaded."
  else
    echo "Error downloading the exports folder."
  fi
fi