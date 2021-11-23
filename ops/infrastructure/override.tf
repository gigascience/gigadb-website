variable "source_dbi_resource_id" {
  type = string
}

variable "utc_restore_time" {
  type = string
  default = null
}

variable "use_latest_restorable_time" {
  type = bool
  default = null
}

module "rds" {
  restore_to_point_in_time = {
    source_dbi_resource_id = var.source_dbi_resource_id
    restore_time = var.utc_restore_time
    use_latest_restorable_time = var.use_latest_restorable_time
  }
}