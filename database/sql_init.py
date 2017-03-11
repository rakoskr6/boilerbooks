import json
import argparse
from peewee import *
from models import Users, Organizations, Budgets, Purchases, Income, Rights

parser = argparse.ArgumentParser(
    description='Initialize the database for a BoilerBooks instance'
)
parser.add_argument('--config-file', type=str, default='config.json', dest='config',
                    help='Location of the configuration file')
parser.add_argument('--delete-tables', dest='delete', action='store_true',
                    help='Delete the tables in the database before recreating. WARNING: DELETES ALL DATA')
args = parser.parse_args()

with open(args.config) as config_file:
    config = json.load(config_file)

db = MySQLDatabase(
    config['MYSQL-DB'],
    host=config['MYSQL-HOST'],
    user=config['MYSQL-USER'],
    password=config['MYSQL-PASS']
)
db.connect()

# List of all models, in order of requirement (Later models may depend on FK on earlier models)
Models = [
    Users, Organizations, Budgets, Purchases, Income, Rights
]

# Delete the tables before inserting, if model changes are happening
if args.delete:
    for model in reversed(Models): # In reverse order for FK constraints
        db.drop_table(model, fail_silently=True) # Fail silently if the table is missing

# Create the models
for model in Models:
    db.create_table(model)

db.close()
