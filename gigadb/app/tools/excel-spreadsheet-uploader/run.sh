#!/usr/bin/env bash

# Set classpath
export PROJECT_HOME="/tool"
export LIB_DIR="$PROJECT_HOME/lib"
export CLASSPATH=".:$LIB_DIR/*"

JAVA_LOG="logs/java.log"
JAVAC_LOG="logs/javac.log"
# Clean up previous bash script runs
rm -f $JAVA_LOG
rm -f $JAVAC_LOG

# Compile Java source files
javac -d $PROJECT_HOME/bin \
  $PROJECT_HOME/src/*.java \
  $PROJECT_HOME/src/Log/*.java \
  $PROJECT_HOME/src/Exception/*.java \
  $PROJECT_HOME/src/Test/*.java \
  &> $JAVAC_LOG

# Execute ExceltoGigaDB tool
java -cp $CLASSPATH:$PROJECT_HOME/configuration:$PROJECT_HOME/bin Main &> $JAVA_LOG