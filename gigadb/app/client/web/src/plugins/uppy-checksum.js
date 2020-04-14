
const { Plugin } = require('@uppy/core')
const ChunkedFileReader = require('chunked-file-reader')
const SparkMD5 = require('spark-md5')

export const Checksum = class Checksum extends Plugin {
  constructor (uppy, opts) {
    super(uppy, opts)
    this.id = opts.id || 'Checksum'
    this.type = 'modifier'

    this.prepareUpload = this.prepareUpload.bind(this)
    this.calcMd5 = this.calcMd5.bind(this)
  }

  calcMd5 (file) {
    this.uppy.log(`[Checksum] Calculating Hash: ${file.name}`)
    return new Promise(function (resolve, reject) {
      var spark  = new SparkMD5.ArrayBuffer(),
          reader = new ChunkedFileReader();

      reader.subscribe('chunk', function (e) {
        spark.append(e.chunk);
      });

      reader.subscribe('end', function (e) {
        // var rawHash    = spark.end(true);
        var rawHash    = spark.end();
        // var base64Hash = btoa(rawHash);
        // this.uppy.log(rawHash)
        // resolve(base64Hash);
        resolve(rawHash);
      });

      reader.readChunks(file.data);
    })
  }

  prepareUpload (fileIDs) {
    const promises = fileIDs.map((fileID) => {
      const file = this.uppy.getFile(fileID)
      this.uppy.log(`[Checksum] prepareUpload for file ${file.name}`)
      return this.calcMd5(file).then((hash) => {
        this.uppy.setFileMeta(fileID, { checksum: hash })
        this.uppy.emit('preprocess-progress', file, {
          mode: 'indeterminate',
          message: `MD5 checksum for ${file.name} done`
        })
      })
    })
    return Promise.all(promises)
  }

  install () {
    this.uppy.addPreProcessor(this.prepareUpload)
  }

  uninstall () {
    this.uppy.removePreProcessor(this.prepareUpload)
  }
}