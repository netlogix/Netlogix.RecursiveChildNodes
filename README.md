# Recursive Child Nodes

The purpose of this package is to ease the creation of new nodeTypes that can have
multiple childNodes but require at least one. A good example might be a nodeType Event
that could have multiple `dates` through a ContentCollection.

To automatically create child nodes inside the `dates` ContentCollection when
creating a new Event, the following configuration could be used:

```yaml
'Lala.NodeTypes:Event':
  superTypes:
    'Neos.Neos:Document': true

  options:
    recursiveChildNodes: # Recursive child nodes can be configured here
      dates: # Name of the first childNode
        -
          type: 'Lala.NodeTypes:EventDate'
          options:
            recursiveChildNodes: # Infinite recursion, yay!
              items:
                -
                  type: 'Lala.NodeTypes:SomeOtherNodeType'

  ui:
    label: 'Event'
    group: 'general'
    icon: 'icon-calendar'

  childNodes:
    dates:
      type: 'Neos.Neos:ContentCollection'
      constraints:
        '*': false
        'Lala.NodeTypes:EventDate': true

    main:
      constraints:
        nodeTypes:
          '*': true
```
