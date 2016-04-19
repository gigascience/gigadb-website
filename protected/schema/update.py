import json
import psycopg2
import sys

from schemup import commands
from schemup.dbs import postgres

dryRun = not (len(sys.argv) > 1 and sys.argv[1] == 'commit')

class DictSchema(object):
    def __init__(self, path):
        self.versions = json.load(open(path, "r"))

    def getExpectedTableVersions(self):
        return sorted(self.versions.iteritems())

dbConfig = json.load(open("../config/db.json", "r"))

pgConn = psycopg2.connect(**dbConfig)

pgSchema = postgres.PostgresSchema(pgConn, dryRun=dryRun)

dictSchema = DictSchema("versions.json")

commands.load('migrations')
sqls = commands.upgrade(pgSchema, dictSchema)
    
if dryRun and sqls:
    print "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
    for sql in sqls: print sql
    print "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
    sys.exit(1)

commands.validate(pgSchema, dictSchema)
