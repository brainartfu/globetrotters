var config = {
  settings: { hasHeaders: false },
  dimensions: { borderWidth: 2 },
  content: [{
    type: 'row',
    content: [{
      type: 'column',
      width: 14,
      content: [{
        type: 'component',
        componentName: 'test'
      }]
    }, {
      type: 'column',
      width: 57,
      content: [{
        type: 'component',
        componentName: 'test',
        height: 32
      }, {
        type: 'component',
        componentName: 'test',
        height: 68
      }]
    }, {
      type: 'column',
      width: 29,
      content: [{
        type: 'component',
        componentName: 'test'
      }]
    },]
  }]
};

var myLayout = new GoldenLayout(JSON.parse(localStorage.getItem('savedState')) || config);

myLayout.registerComponent('test', function (container, state) {
  container.getElement().html( '<h2>sdfsfs</h2>' );
});

myLayout.on('stateChanged', function () {
  var state = JSON.stringify(myLayout.toConfig());
  localStorage.setItem('savedState', state);
});

myLayout.init();