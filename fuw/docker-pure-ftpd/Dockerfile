FROM stilliard/pure-ftpd:hardened

# setup group for uploader and downloader
RUN useradd -g ftpgroup -d /home/uploader -s /dev/null uploader && \
	useradd -g ftpgroup -d /home/downloader -s /dev/null downloader

ENTRYPOINT ["/run.sh"]
CMD ["-l", "puredb:/etc/pure-ftpd/pureftpd.pdb", "-E -j -R", "-P", "$PUBLICHOST"]