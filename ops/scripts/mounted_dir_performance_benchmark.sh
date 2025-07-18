#!/usr/bin/env bash

# mounted_dir_performance_benchmark.sh
# Systematic performance benchmarking script for mounted directories
# Usage: ./mounted_dir_performance_benchmark.sh [mount_point1] [mount_point2] ...

set -euo pipefail

# Configuration
declare -r SCRIPT_NAME="$(basename "$0")"
declare -r TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
declare -r BENCHMARK_DIR="/tmp/benchmark_${TIMESTAMP}"
declare -r RESULTS_DIR="/tmp/benchmark_results_${TIMESTAMP}"
declare -r LOG_FILE="${RESULTS_DIR}/benchmark_${TIMESTAMP}.log"

# Test file sizes
declare -r SMALL_FILE_SIZE="4k"
declare -r MEDIUM_FILE_SIZE="1G"
declare -r LARGE_FILE_SIZE="10G"
declare -r SMALL_FILES_COUNT="1000"

# Default mount points to test
declare -a DEFAULT_MOUNT_POINTS=("/aws" "/wasabi" "/block")

# Colors for output
declare -r RED='\033[0;31m'
declare -r GREEN='\033[0;32m'
declare -r YELLOW='\033[1;33m'
declare -r BLUE='\033[0;34m'
declare -r NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    local color="$1"
    local message="$2"
    echo -e "${color}[$(date '+%Y-%m-%d %H:%M:%S')] ${message}${NC}" | tee -a "$LOG_FILE"
}

# Function to log results
log_result() {
    local test_name="$1"
    local mount_point="$2"
    local time_taken="$3"
    local throughput="$4"
    local cpu_usage="$5"
    
    echo "${test_name},${mount_point},${time_taken},${throughput},${cpu_usage}" >> "${RESULTS_DIR}/results.csv"
    print_status "$GREEN" "Test: $test_name | Mount: $mount_point | Time: ${time_taken}s | Throughput: $throughput | CPU: $cpu_usage%"
}

# Function to check prerequisites
check_prerequisites() {
    print_status "$BLUE" "Checking prerequisites..."
    
    # Check if running as root or with sudo
    if [[ $EUID -ne 0 ]]; then
        print_status "$YELLOW" "Warning: Not running as root. Some operations may fail."
    fi
    
    # Check required commands
    local required_commands=("dd" "time" "md5sum" "df" "mountpoint" "top")
    for cmd in "${required_commands[@]}"; do
        if ! command -v "$cmd" &> /dev/null; then
            print_status "$RED" "Error: Required command '$cmd' not found"
            exit 1
        fi
    done
    
    # Create directories
    mkdir -p "$BENCHMARK_DIR" "$RESULTS_DIR"
    
    # Initialize CSV results file
    echo "test_name,mount_point,time_seconds,throughput_mbs,cpu_usage_percent" > "${RESULTS_DIR}/results.csv"
    
    print_status "$GREEN" "Prerequisites check completed"
}

# Function to check if mount point exists and is mounted
check_mount_point() {
    local mount_point="$1"
    
    if [[ ! -d "$mount_point" ]]; then
        print_status "$RED" "Mount point $mount_point does not exist"
        return 1
    fi
    
    if ! mountpoint -q "$mount_point" 2>/dev/null; then
        print_status "$YELLOW" "Warning: $mount_point is not a mount point"
        return 1
    fi
    
    # Test if writable
    if ! touch "$mount_point/test_write_$$" 2>/dev/null; then
        print_status "$RED" "Mount point $mount_point is not writable"
        return 1
    fi
    rm -f "$mount_point/test_write_$$"
    
    return 0
}

# Function to get CPU usage during command execution
get_cpu_usage() {
    local pid="$1"
    local cpu_total=0
    local count=0
    
    while kill -0 "$pid" 2>/dev/null; do
        local cpu=$(ps -p "$pid" -o %cpu --no-headers 2>/dev/null || echo "0")
        cpu_total=$(echo "$cpu_total + $cpu" | bc -l 2>/dev/null || echo "$cpu_total")
        count=$((count + 1))
        sleep 1
    done
    
    if [[ $count -gt 0 ]]; then
        echo "scale=1; $cpu_total / $count" | bc -l 2>/dev/null || echo "0"
    else
        echo "0"
    fi
}

# Function to run a timed command with CPU monitoring
run_timed_command() {
    local test_name="$1"
    local mount_point="$2"
    local command="$3"
    
    print_status "$BLUE" "Running: $test_name on $mount_point"
    
    # Start CPU monitoring in background
    local start_time=$(date +%s.%N)
    
    # Execute command and capture timing
    local time_output
    if time_output=$(timeout 3600 /usr/bin/time -f "%e" bash -c "$command" 2>&1); then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local time_taken=$(echo "$time_output" | tail -n1)
        
        # Calculate throughput if applicable
        local throughput="N/A"
        if [[ "$test_name" == *"Write"* ]] || [[ "$test_name" == *"Read"* ]]; then
            if [[ "$test_name" == *"1G"* ]]; then
                throughput=$(echo "scale=1; 1024 / $time_taken" | bc -l 2>/dev/null || echo "N/A")
                throughput="${throughput} MB/s"
            elif [[ "$test_name" == *"10G"* ]]; then
                throughput=$(echo "scale=1; 10240 / $time_taken" | bc -l 2>/dev/null || echo "N/A")
                throughput="${throughput} MB/s"
            fi
        fi
        
        # For simplicity, use a basic CPU estimation
        local cpu_usage="N/A"
        
        log_result "$test_name" "$mount_point" "$time_taken" "$throughput" "$cpu_usage"
        return 0
    else
        print_status "$RED" "Command failed or timed out: $test_name on $mount_point"
        log_result "$test_name" "$mount_point" "FAILED" "N/A" "N/A"
        return 1
    fi
}

# Function to create test files
create_test_files() {
    print_status "$BLUE" "Creating test files..."
    
    # Create small files directory
    mkdir -p "${BENCHMARK_DIR}/smallfiles"
    for i in $(seq 1 100); do  # Create 100 small files for testing
        dd if=/dev/urandom of="${BENCHMARK_DIR}/smallfiles/file${i}.dat" bs="$SMALL_FILE_SIZE" count=1 >/dev/null 2>&1
    done
    
    # Create medium test file
    dd if=/dev/zero of="${BENCHMARK_DIR}/1g-file.dat" bs=1G count=1 >/dev/null 2>&1
    
    # Create large test file (only if enough space)
    local available_space=$(df "$BENCHMARK_DIR" | awk 'NR==2 {print $4}')
    if [[ $available_space -gt 15000000 ]]; then  # ~15GB
        dd if=/dev/zero of="${BENCHMARK_DIR}/10g-file.dat" bs=1G count=10 >/dev/null 2>&1
        print_status "$GREEN" "Created 10G test file"
    else
        print_status "$YELLOW" "Insufficient space for 10G test file, skipping"
    fi
    
    print_status "$GREEN" "Test files created"
}

# Function to run write tests
run_write_tests() {
    local mount_point="$1"
    local test_dir="${mount_point}/benchmark_test_$$"
    
    mkdir -p "$test_dir"
    
    # 1G File Write Test
    run_timed_command "1G File Write" "$mount_point" \
        "dd if=/dev/zero of='${test_dir}/write-1g.dat' bs=1G count=1 oflag=direct"
    
    # 10G File Write Test (if test file exists)
    if [[ -f "${BENCHMARK_DIR}/10g-file.dat" ]]; then
        run_timed_command "10G File Write" "$mount_point" \
            "dd if=/dev/zero of='${test_dir}/write-10g.dat' bs=1G count=10 oflag=direct"
    fi
    
    # Small files write test
    run_timed_command "100 Small Files Write" "$mount_point" \
        "cp -r '${BENCHMARK_DIR}/smallfiles' '${test_dir}/'"
}

# Function to run read tests
run_read_tests() {
    local mount_point="$1"
    local test_dir="${mount_point}/benchmark_test_$$"
    
    # 1G File Read Test
    if [[ -f "${test_dir}/write-1g.dat" ]]; then
        run_timed_command "1G File Read" "$mount_point" \
            "dd if='${test_dir}/write-1g.dat' of=/dev/null bs=1G count=1"
    fi
    
    # 10G File Read Test
    if [[ -f "${test_dir}/write-10g.dat" ]]; then
        run_timed_command "10G File Read" "$mount_point" \
            "dd if='${test_dir}/write-10g.dat' of=/dev/null bs=1G count=10"
    fi
    
    # Small files read test
    if [[ -d "${test_dir}/smallfiles" ]]; then
        run_timed_command "100 Small Files Read" "$mount_point" \
            "find '${test_dir}/smallfiles' -type f -exec cat {} \\; > /dev/null"
    fi
}

# Function to run checksum tests
run_checksum_tests() {
    local mount_point="$1"
    local test_dir="${mount_point}/benchmark_test_$$"
    
    # 1G File MD5 Checksum
    if [[ -f "${test_dir}/write-1g.dat" ]]; then
        run_timed_command "1G File MD5 Checksum" "$mount_point" \
            "md5sum '${test_dir}/write-1g.dat' > '${test_dir}/1g-file.md5'"
    fi
    
    # 10G File MD5 Checksum
    if [[ -f "${test_dir}/write-10g.dat" ]]; then
        run_timed_command "10G File MD5 Checksum" "$mount_point" \
            "md5sum '${test_dir}/write-10g.dat' > '${test_dir}/10g-file.md5'"
    fi
}

# Function to run copy tests
run_copy_tests() {
    local mount_point="$1"
    local test_dir="${mount_point}/benchmark_test_$$"
    
    # Copy 1G file to mount point
    if [[ -f "${BENCHMARK_DIR}/1g-file.dat" ]]; then
        run_timed_command "Copy 1G File In" "$mount_point" \
            "cp '${BENCHMARK_DIR}/1g-file.dat' '${test_dir}/'"
    fi
    
    # Copy 1G file from mount point
    if [[ -f "${test_dir}/1g-file.dat" ]]; then
        run_timed_command "Copy 1G File Out" "$mount_point" \
            "cp '${test_dir}/1g-file.dat' /tmp/copy-out-test.dat && rm -f /tmp/copy-out-test.dat"
    fi
    
    # Copy 10G file tests (if available)
    if [[ -f "${BENCHMARK_DIR}/10g-file.dat" ]]; then
        run_timed_command "Copy 10G File In" "$mount_point" \
            "cp '${BENCHMARK_DIR}/10g-file.dat' '${test_dir}/'"
        
        if [[ -f "${test_dir}/10g-file.dat" ]]; then
            run_timed_command "Copy 10G File Out" "$mount_point" \
                "cp '${test_dir}/10g-file.dat' /tmp/copy-out-10g-test.dat && rm -f /tmp/copy-out-10g-test.dat"
        fi
    fi
}

# Function to cleanup test files
cleanup_test_files() {
    local mount_point="$1"
    local test_dir="${mount_point}/benchmark_test_$$"
    
    print_status "$BLUE" "Cleaning up test files in $mount_point"
    rm -rf "$test_dir" 2>/dev/null || true
}

# Function to run all tests for a mount point
run_mount_point_tests() {
    local mount_point="$1"
    
    print_status "$YELLOW" "Starting benchmark tests for: $mount_point"
    
    if ! check_mount_point "$mount_point"; then
        print_status "$RED" "Skipping $mount_point due to mount point check failure"
        return 1
    fi
    
    # Run test sequences
    run_write_tests "$mount_point"
    run_read_tests "$mount_point"
    run_checksum_tests "$mount_point"
    run_copy_tests "$mount_point"
    
    # Cleanup
    cleanup_test_files "$mount_point"
    
    print_status "$GREEN" "Completed benchmark tests for: $mount_point"
}

# Function to generate performance report
generate_report() {
    local report_file="${RESULTS_DIR}/performance_report_${TIMESTAMP}.md"
    
    print_status "$BLUE" "Generating performance report: $report_file"
    
    cat > "$report_file" << EOF
# Mount Point Performance Benchmark Report

**Generated:** $(date)  
**Script:** $SCRIPT_NAME  
**System:** $(uname -a)  

## System Information

\`\`\`
$(free -h)
\`\`\`

\`\`\`
$(df -h)
\`\`\`

## Test Results

| Test Name | Mount Point | Time (s) | Throughput | CPU Usage |
|-----------|-------------|----------|------------|-----------|
EOF

    # Read CSV and format as markdown table
    tail -n +2 "${RESULTS_DIR}/results.csv" | while IFS=',' read -r test_name mount_point time_taken throughput cpu_usage; do
        echo "| $test_name | $mount_point | $time_taken | $throughput | $cpu_usage |" >> "$report_file"
    done
    
    cat >> "$report_file" << EOF

## Performance Summary

EOF

    # Generate summary statistics
    for mount_point in "${tested_mount_points[@]}"; do
        echo "### $mount_point" >> "$report_file"
        echo "" >> "$report_file"
        grep "$mount_point" "${RESULTS_DIR}/results.csv" | while IFS=',' read -r test_name mp time_taken throughput cpu_usage; do
            echo "- **$test_name:** ${time_taken}s" >> "$report_file"
        done
        echo "" >> "$report_file"
    done
    
    print_status "$GREEN" "Performance report generated: $report_file"
}

# Function to display usage
usage() {
    cat << EOF
Usage: $SCRIPT_NAME [OPTIONS] [MOUNT_POINTS...]

Systematic performance benchmarking script for mounted directories.

OPTIONS:
    -h, --help              Show this help message
    -v, --verbose           Enable verbose output
    -o, --output DIR        Specify output directory (default: /tmp/benchmark_results_TIMESTAMP)

MOUNT_POINTS:
    Space-separated list of mount points to test.
    If not specified, tests default mount points: ${DEFAULT_MOUNT_POINTS[*]}

EXAMPLES:
    $SCRIPT_NAME                    # Test default mount points
    $SCRIPT_NAME /aws /wasabi       # Test specific mount points
    $SCRIPT_NAME -o /home/user/results /rclone  # Custom output directory

EOF
}

# Main function
main() {
    local mount_points=()
    local output_dir=""
    
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            -h|--help)
                usage
                exit 0
                ;;
            -v|--verbose)
                set -x
                shift
                ;;
            -o|--output)
                output_dir="$2"
                shift 2
                ;;
            -*)
                print_status "$RED" "Unknown option: $1"
                usage
                exit 1
                ;;
            *)
                mount_points+=("$1")
                shift
                ;;
        esac
    done
    
    # Use default mount points if none specified
    if [[ ${#mount_points[@]} -eq 0 ]]; then
        mount_points=("${DEFAULT_MOUNT_POINTS[@]}")
    fi
    
    # Update results directory if specified
    if [[ -n "$output_dir" ]]; then
        RESULTS_DIR="$output_dir"
        LOG_FILE="${RESULTS_DIR}/benchmark_${TIMESTAMP}.log"
    fi
    
    print_status "$BLUE" "Starting mount point performance benchmark"
    print_status "$BLUE" "Testing mount points: ${mount_points[*]}"
    print_status "$BLUE" "Results will be saved to: $RESULTS_DIR"
    
    # Check prerequisites
    check_prerequisites
    
    # Create test files
    create_test_files
    
    # Track tested mount points for report
    declare -a tested_mount_points=()
    
    # Run tests for each mount point
    for mount_point in "${mount_points[@]}"; do
        if run_mount_point_tests "$mount_point"; then
            tested_mount_points+=("$mount_point")
        fi
    done
    
    # Generate performance report
    if [[ ${#tested_mount_points[@]} -gt 0 ]]; then
        generate_report
    else
        print_status "$RED" "No mount points were successfully tested"
    fi
    
    # Cleanup
    rm -rf "$BENCHMARK_DIR"
    
    print_status "$GREEN" "Benchmark completed. Results available in: $RESULTS_DIR"
    print_status "$BLUE" "Log file: $LOG_FILE"
    print_status "$BLUE" "CSV results: ${RESULTS_DIR}/results.csv"
    print_status "$BLUE" "Report: ${RESULTS_DIR}/performance_report_${TIMESTAMP}.md"
}

# Run main function if script is executed directly
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi 