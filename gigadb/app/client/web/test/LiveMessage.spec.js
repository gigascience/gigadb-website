import { makeFactory } from './utils'
import LiveMessage from '../src/components/LiveMessage.vue'

const factory = makeFactory(LiveMessage)

describe('LiveMessage component', function () {
  beforeEach(function() {
    jasmine.clock().install();
  });

  afterEach(function() {
    jasmine.clock().uninstall();
  });

  it('renders correctly with default props', () => {
    const wrapper = factory()
    expect(wrapper.text()).toBe('')
    expect(wrapper.attributes('aria-live')).toBe('polite')
  })

  it('accepts valid politeness values', () => {
    const wrapper = factory({
      propsData: {
        politeness: 'assertive'
      }
    })
    expect(wrapper.attributes('aria-live')).toBe('assertive')
  })

  it('updates the message after a delay', done => {
    const wrapper = factory({
      propsData: {
        message: 'Initial Message'
      }
    });
    wrapper.setProps({ message: 'Updated Message' });
    wrapper.vm.$nextTick(() => {
      expect(wrapper.text()).toBe('');
      jasmine.clock().tick(600);
      wrapper.vm.$nextTick(() => {
        expect(wrapper.text()).toBe('Updated Message');
        done();
      });
    });
  });
})