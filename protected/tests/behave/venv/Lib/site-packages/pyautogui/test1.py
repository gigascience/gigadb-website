@property
def foo():
    return _foo

@foo.setter
def foo(value):
    _foo = value

_foo = 'foo'
