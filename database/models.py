from peewee import *

""" Mixins for all the models
"""

class ModifiedTimestamp(Field):
    """ Adds a modified timestamp to peewee, since it is supported by default.

    Attributes:
        db_field (string): The name of the MySQL DB Type
        default (string): default type
    """

    db_field = 'timestamp'
    default = 'CURRENT_TIMESTAMP'

class Base(Model):
    """ Base class for all the other models. Adds modified time to all models.
    More will probably be added to this later.

    Attributes:
        modified_time (timestamp): When the row was last changed
    """

    modified_time = ModifiedTimestamp()



""" Boilerbooks database models.
"""

class Users(Base):
    """ The Users table. Holds basic information about each user.

    Attributes:
        username (CharField): Max length 16 chars, is the primary key
        password (CharField/Password Hash): The hashed password
        name (CharField): Full name of user
        email (CharField): Email
        address (CharField): Full street address (For where checks can be sent to user)
        cert (CharField): Location of the BOSO purchase certification on the local filesystem. It
            can be false, if it was deleted on not uploaded during registration.
        revoke_counter (IntegerField): A counter that increments whenever tokens are revoked, thus
            invalidating all old JWT.
    """

    username = CharField(max_length=16, primary_key=True)
    password = CharField(max_length=512)
    name = CharField(max_length=200)
    email = CharField(max_length=100)
    address = CharField(max_length=400)
    cert = CharField(max_length=300, null=True)
    revoke_counter = IntegerField(default=0)

class Organizations(Base):
    """ The Organizations table. Every budget, purchase and income must be
    associated with an organization. An organization needs to have a name, and
    can optionally have a parent organization.

    Attributes:
        name (CharField): Organization name
        parent (ForeignKey on name): The parent of this organization (optional)
    """

    name = CharField(max_length=100, primary_key=True)
    parent = ForeignKeyField('self', null=True, default=None, db_column='parent')

class Budgets(Base):
    """ A budget for an organization. An organization can have multiple
    budgets, but they are unique on the organization, name and year columns.

    Attributes:
        organization (ForeignKey on an Organizations): Who owns the organization
        name (CharField): Name of this budget
        year (IntegerField): The fiscal year this is valid for (Starts at Fall Semester)
        amount (DecimalField): The monetary amount for this budget
    """

    organization = ForeignKeyField(Organizations, db_column='organization')
    name = CharField(max_length=250)
    year = IntegerField()
    amount = DecimalField(decimal_places=2)

    class Meta:
        # Create a unique index on organization, name and year
        indexes = (
            (('organization', 'name', 'year'), True),
        )

class Purchases(Base):
    """ The Purchase table. Holds information about a purchase.

    Attributes:
        username (ForeignKey on a User): Who made this purchase
        approved_by (ForeignKey on a User): Who approved this purchase
        budget (ForeignKey on a budget): What budget this belongs to
        purchase_date (DateTimeField): When it was purchased
        item (CharField): What was purchased
        reason (CharField): Why was it purchased
        vendor (CharField): Who was it purchased from
        amount (DecimalField): Monetary amount of purchase
        status (IntegerField): Reimbursement status: (
            1: 'Requested', 2: 'Approved', 3: 'Denied', 4: 'Purchased',
            5: 'Processing Reimbursement', 6: 'Reimbursed'
        )
        funding_source (IntegerField): The source of income it come from: (
            1: 'BOSO', 2: 'Cash'
        )
        comments (DecimalField): Extra comments
    """

    username = ForeignKeyField(Users, db_column='username', related_name='bought_by')
    approved_by = ForeignKeyField(Users, db_column='approved_by', related_name='approved_by')
    budget = ForeignKeyField(Budgets)
    purchase_date = DateTimeField()
    item = CharField(max_length=100)
    reason = CharField(max_length=100)
    vendor = CharField(max_length=100)
    receipt = CharField(max_length=255)
    amount = DecimalField(decimal_places=2)
    status = IntegerField()
    funding_source = IntegerField()
    comments = CharField(max_length=1000)

class Income(Base):
    """ The Income table. Holds information about income.

    Attributes:
        username (ForeignKey on a User): Who added this income
        budget (ForeignKey on a budget): What budget this belongs to
        source (CharField): Where it came from
        type (IntegerField): What type of income it is: (
            1: 'BOSO', 2: 'Cash', 3: 'Discound'
        )
        amount (DecimalField): Monetary amount of income
        info (CharField): Budget 1-line information
        status (IntegerField): Income status: (
            1: 'Expected', 2: 'Received',
        )
        comments (DecimalField): Extra comments
    """

    username = ForeignKeyField(Users, db_column='username')
    budget = ForeignKeyField(Budgets)
    source = CharField(max_length=250)
    type = IntegerField()
    amount = DecimalField(decimal_places=2)
    item = CharField(max_length=100)
    status = IntegerField()
    comments = CharField(max_length=1000)

class Rights(Base):
    """ The Rights table. Holds read and write permissions on certain actions.
    The budget_* fields are logically AND-ed together to check if they have
    permissions.

    Attributes:
        username (ForeignKey on a User): The user for this permission
        budget_organization (CharField): The budget organization they can act on or '*' for all
        budget_name (CharField): The budget name they can act on or '*' for all
        budget_year (IntegerField): The budget year they can act on or '*' for all
        target (IntegerField): What action this permission is targeting: (
            1: 'Purchases', 2: 'Income', 3: 'Budgets',
            4: 'Rights', 5: 'Everything'
        )
        permission: (IntegerField): Read or Read/Write Permission: (
            1: 'Read', 2: 'Read/Write'
        )
        extra: (CharField): Extra information that is returned on rights check,
            for additional validation
    """

    username = ForeignKeyField(Users, db_column='username')
    budget_organization = CharField()
    budget_name = CharField()
    budget_year = IntegerField()
    target = IntegerField()
    permission = IntegerField()
    extra = CharField(max_length=1000)
