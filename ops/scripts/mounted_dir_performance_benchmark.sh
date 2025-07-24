#!/usr/bin/env bash

# Simple mount point performance benchmark script
# Usage: ./mounted_dir_performance_benchmark.sh [mount_points...]

set -euo pipefail

# Configuration
SCRIPT_NAME="$(basename "$0")"
TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
BENCHMARK_DIR="/home/ec2-user/benchmark"
RESULTS_DIR="${BENCHMARK_DIR}/results"
LOG_FILE="${BENCHMARK_DIR}/benchmark.log"
CPU_FILE="${BENCHMARK_DIR}/cpu_usage.txt"

# Mount point configurations
declare -A MOUNT_PATHS=(
    ["/aws"]="/aws/share/dropbox/user111"
    ["/wasabi"]="/wasabi/share/dropbox/user999"
    ["/block"]="/block/share/dropbox/user444"
    ["/userdropboxes"]="/userdropboxes/share/dropbox/user666"
)

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Logging function
log() {
    local level="$1"
    local message="$2"
    local timestamp="$(date '+%Y-%m-%d %H:%M:%S')"
    echo -e "${level}[${timestamp}] ${message}${NC}" | tee -a "$LOG_FILE"
}

# Create test data directory and files
create_test_data() {
    local test_data_dir="${BENCHMARK_DIR}/test-data"
    mkdir -p "$test_data_dir"
    mkdir -p "${test_data_dir}/smallfiles"
    
    # Create 1G test file
    if [[ ! -f "$test_data_dir/1g-file.dat" ]]; then
        log "$BLUE" "Creating 1G test file..."
        dd if=/dev/zero of="$test_data_dir/1g-file.dat" bs=1G count=1 &>/dev/null
    fi
    log "$BLUE" "1g file already created in ${test_data_dir}"
    
    # Create 10G test file if there's enough space
    if [[ ! -f "$test_data_dir/10g-file.dat" ]]; then
        local available_space=$(df /tmp | awk 'NR==2 {print $4}')
        if [[ $available_space -gt 15000000 ]]; then
            log "$BLUE" "Creating 10G test file..."
            dd if=/dev/zero of="$test_data_dir/10g-file.dat" bs=1G count=10 &>/dev/null
        else
            log "$YELLOW" "Insufficient space for 10G test file, skipping"
        fi
    fi
    log "$BLUE" "10g file already created in ${test_data_dir}"

    # Create 5000 small files (1KB each)
    if  [[ ! -d "${test_data_dir}/smallfiles" ]] || [[ $(find "${test_data_dir}/smallfiles" -maxdepth 1 -type f -printf '.' 2>/dev/null | wc -c) -lt 5000 ]]; then
        log "$BLUE" "Creating 5000 small files..."
        for i in $(seq 1 5000); do
            fallocate -l 1024 "${test_data_dir}/smallfiles/file${i}.dat" &>/dev/null 2>&1
        done
    fi
    log "$BLUE" "5000 small files already created in ${test_data_dir}/smallfiles"
    
    echo "$test_data_dir"
}

# Test functions based on exact commands from documentation table
test_1g_file_write() {
    local mount_path="$1"
    log "$BLUE" "1G File Write: dd if=/dev/zero of=${mount_path}/1g-file.dat bs=1G count=1 oflag=direct"
    local start_time=$(date +%s.%N)
    
    if /usr/bin/time dd if=/dev/zero of="${mount_path}/1g-file.dat" bs=1G count=1 oflag=direct 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local throughput=$(echo "scale=0; 1024 / $duration" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE} | grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "1G File Write,$(printf "%s,%s,%s" "$duration" "$throughput" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "1G File Write,FAILED,N/A"
        return 1
    fi
}

test_10g_file_write() {
    local mount_path="$1"
    log "$BLUE" "10G File Write: dd if=/dev/zero of=${mount_path}/10g-file.dat bs=1G count=10 oflag=direct"
    local start_time=$(date +%s.%N)
    
    if (/usr/bin/time dd if=/dev/zero of="${mount_path}/10g-file.dat" bs=1G count=10 oflag=direct 2> ${CPU_FILE}); then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local throughput=$(echo "scale=0; 10240 / $duration" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE} | grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "10G File Write,$(printf "%s,%s,%s" "$duration" "$throughput" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "10G File Write,FAILED,N/A"
        return 1
    fi
}

test_1g_file_read() {
    local mount_path="$1"
    
    log "$BLUE" "1G File Read: if=${mount_path}/1g-file.dat of=/dev/null bs=1G count=1 "
    local start_time=$(date +%s.%N)
    
    if /usr/bin/time dd if="${mount_path}/1g-file.dat" of=/dev/null bs=1G count=1 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local throughput=$(echo "scale=0; 1024 / $duration" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE} | grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "1G File Read,$(printf "%s,%s,%s" "$duration" "$throughput" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "1G File Read,FAILED,N/A"
        return 1
    fi
}

test_10g_file_read() {
    local mount_path="$1"
    
    log "$BLUE" "10G File Read: dd if=${mount_path}/10g-file.dat of=/dev/null bs=1G count=10"
    local start_time=$(date +%s.%N)
    
    if /usr/bin/time dd if="${mount_path}/10g-file.dat" of=/dev/null bs=1G count=10 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local throughput=$(echo "scale=0; 10240 / $duration" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE} | grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "10G File Read,$(printf "%s,%s,%s" "$duration" "$throughput" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "10G File Read,FAILED,N/A"
        return 1
    fi
}

test_move_in_5000_small_files() {
    local mount_path="$1"
    mkdir -p "$mount_path/smallfiles"
    
    log "$BLUE" "Move in 5000 small files: cp -v ${BENCHMARK_DIR}/test-data/smallfiles/* smallfiles/"
    local start_time=$(date +%s.%N)
    
    if /usr/bin/time cp "${BENCHMARK_DIR}/test-data/smallfiles"/* "$mount_path/smallfiles/" 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE}| grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "Move in 5000 small files,$(printf "%s,%s" "$duration" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "Move in 5000 small files,FAILED,N/A"
        return 1
    fi
}

test_move_out_5000_small_files() {
    local mount_path="$1"
    if [[ ! -d "$mount_path/smallfiles" ]]; then
        echo "Move out 5000 small files,SKIPPED,N/A"
        return 1
    fi
    
    log "$BLUE" "Move out 5000 small files: cp -v smallfiles/* ${BENCHMARK_DIR}/smallfiles_out/"
    local start_time=$(date +%s.%N)
    
    if /usr/bin/time cp "${mount_path}/smallfiles"/* "${BENCHMARK_DIR}/smallfiles_out/" 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE} | grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "Move out 5000 small files,$(printf "%s,%s" "$duration" "$cpu_usage")"
        return 0
    else
        echo "Move out 5000 small files,FAILED,N/A"
        return 1
    fi
}

test_move_in_1g_file() {
    local mount_path="$1"
    
    log "$BLUE" "Move in 1 1G file: cp ${BENCHMARK_DIR}/test-data/1g-file.dat ."
    local start_time=$(date +%s.%N)
    
    if /usr/bin/time cp "${BENCHMARK_DIR}/test-data/1g-file.dat" "${mount_path}/copied-1g-file.dat" 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE}| grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "Move in 1 1G file,$(printf "%s,%s" "$duration" "$cpu_usage")"
        return 0
    else
        echo "Move in 1 1G file,FAILED,N/A"
        return 1
    fi
}

test_md5sum_1g_file() {
    local mount_path="$1"
    if [[ ! -f "${mount_path}/copied-1g-file.dat" ]]; then
        echo "md5sum Checksum 1G file,SKIPPED,N/A"
        return 1
    fi
    
    log "$BLUE" "md5sum Checksum 1G file: md5sum copied-1g-file.dat > ${mount_path}copied-1g-file.md5"
    local start_time=$(date +%s.%N)
    
    if cd ${mount_path} && /usr/bin/time md5sum copied-1g-file.dat > ${mount_path}/copied-1g-file.md5 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE}| grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "md5sum Checksum 1G file,$(printf "%s,%s" "$duration" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "md5sum Checksum 1G file,FAILED,N/A"
        return 1
    fi
}

test_move_out_1g_file() {
    local mount_path="$1"
    if [[ ! -f "$mount_path/copied-1g-file.dat" ]]; then
        echo "Move out 1 1G file,SKIPPED,N/A"
        return 1
    fi
    
    log "$BLUE" "Move out 1 1G file: cp copied-1g-file.dat /dev/null"
    local start_time=$(date +%s.%N)
    
    if cd "${mount_path}" && /usr/bin/time cp copied-1g-file.dat /dev/null 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE} | grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "Move out 1 1G file,$(printf "%s,%s" "$duration" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "Move out 1 1G file,FAILED,N/A"
        return 1
    fi
}

test_move_in_10g_file() {
    local mount_path="$1"
    if [[ ! -f "${BENCHMARK_DIR}/test-data/10g-file.dat" ]]; then
        echo "Move in 1 10G file,SKIPPED,N/A"
        return 1
    fi
    
    log "$BLUE" "Move in 1 10G file: cp ${BENCHMARK_DIR}/test-data/10g-file.dat ."
    local start_time=$(date +%s.%N)
    
    if /usr/bin/time cp "${BENCHMARK_DIR}/test-data/10g-file.dat" "${mount_path}/copied-10g-file.dat" 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE} | grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "Move in 1 10G file,$(printf "%s,%s" "$duration" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "Move in 1 10G file,FAILED,N/A"
        return 1
    fi
}

test_md5sum_10g_file() {
    local mount_path="$1"
    if [[ ! -f "${mount_path}/copied-10g-file.dat" ]]; then
        echo "md5sum Checksum 10G file,SKIPPED,N/A"
        return 1
    fi
    
    log "$BLUE" "md5sum Checksum 10G file: md5sum copied-10g-file.dat > copied-10g-file.md5"
    local start_time=$(date +%s.%N)
    
    if cd "${mount_path}" && /usr/bin/time md5sum copied-10g-file.dat > ${mount_path}/copied-10g-file.md5 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE} | grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "md5sum Checksum 10G file,$(printf "%s,%s" "$duration" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "md5sum Checksum 10G file,FAILED,N/A"
        return 1
    fi
}

test_move_out_10g_file() {
    local mount_path="$1"
    if [[ ! -f "${mount_path}/copied-10g-file.dat" ]]; then
        echo "Move out 1 10G file,SKIPPED,N/A"
        return 1
    fi
    
    log "$BLUE" "Move out 1 10G file: cp 10g-file.dat /dev/null"
    local start_time=$(date +%s.%N)
    
    if cd "${mount_path}" && /usr/bin/time cp copied-10g-file.dat /dev/null 2> ${CPU_FILE}; then
        local end_time=$(date +%s.%N)
        local duration=$(echo "$end_time - $start_time" | bc -l)
        local cpu_usage=$(cat ${CPU_FILE} | grep "CPU" | cut -d ' ' -f4 | sed "s/%CPU//")
        echo "Move out 1 10G file,$(printf "%s,%s" "$duration" "$cpu_usage")"
        rm -f ${CPU_FILE}
        return 0
    else
        echo "Move out 1 10G file,FAILED,N/A"
        return 1
    fi
}

# Run all tests for a mount point
run_benchmark() {
    local mount_point="$1"
    local mount_path="${MOUNT_PATHS[$mount_point]}"
    
    log "$YELLOW" "=== Benchmarking $mount_point at $mount_path ==="
    
    # Check if mount point exists and is mounted
    if [[ ! -d "$mount_point" ]]; then
        log "$RED" "Mount point $mount_point does not exist"
        return 1
    fi
    
    if ! mountpoint -q "$mount_point" 2>/dev/null; then
        log "$YELLOW" "Warning: $mount_point is not a mount point"
    fi
    
    # Use mount path directly (no subdirectory creation)
    if ! mkdir -p "$mount_path" 2>/dev/null; then
        log "$RED" "Failed to access mount path: $mount_path"
        return 1
    fi
    
    log "$GREEN" "Using mount path directly: $mount_path"
    
    # Initialize results for this mount point
    local results_file="${RESULTS_DIR}/${mount_point##*/}_results.csv"
    echo "Test,Duration_Seconds,Throughput_MB_per_s,CPU_Usage_%" > "$results_file"
    
    # Run tests in the order specified in the documentation table
    {
        test_1g_file_write "$mount_path"
        test_10g_file_write "$mount_path"
        test_1g_file_read "$mount_path"
        test_10g_file_read "$mount_path"
        test_move_in_5000_small_files "$mount_path"
        test_move_out_5000_small_files "$mount_path"
        test_move_in_1g_file "$mount_path"
        test_md5sum_1g_file "$mount_path"
        test_move_out_1g_file "$mount_path"
        test_move_in_10g_file "$mount_path"
        test_md5sum_10g_file "$mount_path"
        test_move_out_10g_file "$mount_path"
    } >> "$results_file"
    
    # Cleanup test files from mount path
#    log "$BLUE" "Cleaning up test files from: $mount_path"
#    rm -f "$mount_path"/efs-test-write.dat "$mount_path"/*g-file.dat "$mount_path"/*g-file.md5 2>/dev/null || true
#    rm -rf "$mount_path/smallfiles" 2>/dev/null || true
    
    log "$GREEN" "Benchmark completed for $mount_point"
    log "$BLUE" "Results saved to: $results_file"
}

# Generate summary report
generate_report() {
    local report_file="${RESULTS_DIR}/summary_report.md"
    
    log "$BLUE" "Generating summary report..."
    
    cat > "$report_file" << EOF
# Mount Point Performance Benchmark Report

**Generated:** $(date)  
**Script:** $SCRIPT_NAME  

## System Information

\`\`\`
$(uname -a)
$(free -h)
$(df -h | grep -E '/(aws|wasabi|block|userdropboxes)')
\`\`\`

## Test Results

EOF

    # Add results from each mount point
    for results_file in "${RESULTS_DIR}"/*_results.csv; do
        if [[ -f "$results_file" ]]; then
            local mount_name=$(basename "$results_file" _results.csv)
            echo "### $mount_name" >> "$report_file"
            echo "" >> "$report_file"
            echo "| Test | Duration (s) | Throughput (MB/s) | CPU Usage (%) |" >> "$report_file"
            echo "|------|--------------|-------------------|----------------|" >> "$report_file"
            
            tail -n +2 "$results_file" | while IFS=',' read -r test duration throughput; do
                echo "| $test | $duration | $throughput |" >> "$report_file"
            done
            echo "" >> "$report_file"
        fi
    done
    
    cat >> "$report_file" << EOF

## Notes

- Tests performed in the exact order specified in the documentation
- All commands match the reference table exactly
- Duration is measured in seconds
- Throughput is calculated as data_size / duration where applicable

Generated by $SCRIPT_NAME on $(date)
EOF

    log "$GREEN" "Report generated: $report_file"
}

# Main function
main() {
    local mount_points=()
    
    # Parse arguments
    if [[ $# -eq 0 ]]; then
        mount_points=("/aws" "/wasabi" "/block" "/userdropboxes")
    else
        mount_points=("$@")
    fi
    
    # Create results directory
    mkdir -p "$RESULTS_DIR"
    
    log "$BLUE" "Starting benchmark for mount points: ${mount_points[*]}"
    log "$BLUE" "Results directory: $RESULTS_DIR"
    
    # Check prerequisites
    local required_commands=("dd" "bc" "mountpoint" "md5sum" "cp" "/usr/bin/time" "seq" "fallocate")
    for cmd in "${required_commands[@]}"; do
        if ! command -v "$cmd" &> /dev/null; then
            log "$RED" "Error: Required command '$cmd' not found"
            exit 1
        fi
    done
    
    # Create test data
    local test_data_dir
    test_data_dir=$(create_test_data)
    log "$GREEN" "Test data prepared in: $test_data_dir"
    
    # Run benchmarks
    local successful_tests=0
    for mount_point in "${mount_points[@]}"; do
        # Remove trailing slash and validate
        mount_point="${mount_point%/}"
        
        if [[ -n "${MOUNT_PATHS[$mount_point]:-}" ]]; then
            if run_benchmark "$mount_point"; then
                successful_tests=$((successful_tests + 1))
            fi
        else
            log "$RED" "Unknown mount point: $mount_point"
            log "$BLUE" "Available mount points: ${!MOUNT_PATHS[*]}"
        fi
    done
    
    # Generate report if we had successful tests
    if [[ $successful_tests -gt 0 ]]; then
        generate_report
    else
        log "$RED" "No benchmarks completed successfully"
    fi
    
    log "$BLUE" "Cleanup cache files..."
    # rm -rf "${BENCHMARK_DIR}/test-data" "${BENCHMARK_DIR}/smallfiles_out" 2>/dev/null || true
    rm -rf /tmp/cache/rclone* 2>/dev/null || true
    
    log "$GREEN" "Benchmark completed!"
    log "$BLUE" "Results available in: $RESULTS_DIR"
    log "$BLUE" "Log file: $LOG_FILE"
}

# Show usage information
usage() {
    cat << EOF
Usage: $SCRIPT_NAME [mount_points...]

Performance benchmark for mounted directories using exact commands from documentation.

MOUNT_POINTS:
    /aws            - S3 mount point
    /wasabi         - Wasabi mount point  
    /block          - Block storage mount point
    /userdropboxes  - EFS mount point

EXAMPLES:
    $SCRIPT_NAME                    # Test all mount points
    $SCRIPT_NAME /aws /wasabi       # Test specific mount points
    $SCRIPT_NAME /userdropboxes     # Test single mount point

TESTS PERFORMED (in order):
    1. 1G File Write              (dd with oflag=direct)
    2. 10G File Write             (dd with oflag=direct)
    3. 1G File Read               (dd to /dev/null)
    4. 10G File Read              (dd to /dev/null)
    5. Move in 5000 small files   (cp from ${BENCHMARK_DIR}/test-data/smallfiles)
    6. Move out 5000 small files  (cp to ${BENCHMARK_DIR}/test-data/smallfiles)
    7. Move in 1 1G file          (cp from ${BENCHMARK_DIR}/test-data/1g-file.dat)
    8. md5sum Checksum 1G file    (md5sum)
    9. Move out 1 1G file         (cp to /dev/null)
    10. Move in 1 10G file        (cp from ${BENCHMARK_DIR}/test-data/10g-file.dat)
    11. md5sum Checksum 10G file  (md5sum)
    12. Move out 1 10G file       (cp to /dev/null)

EOF
}

# Handle help option
if [[ "${1:-}" == "-h" ]] || [[ "${1:-}" == "--help" ]]; then
    usage
    exit 0
fi

# Run main function
main "$@" 