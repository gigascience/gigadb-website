import { setup } from './setup.js'
import { mount } from '@vue/test-utils'
import { makeFactory } from './utils'
import AttributeSpecifier from '../src/components/AttributeSpecifier.vue'

setup()

const factory = makeFactory(AttributeSpecifier, {
  mountFnc: mount
})

describe("AttributeSpecifier", function () {
  const addedAttributes = [
    { "name": "Luminosity", "value": "400", "unit": "Lux" },
    { "name": "Contrast", "value": "3000", "unit": "Nits" },
  ]

  beforeEach(function () {
    this.renderedComponent = factory({
      propsData: {
        fileAttributes: JSON.parse(JSON.stringify(addedAttributes)) //we need a copy, not reference
      }
    })
  })
  it('should have a named form for adding a new attribute', function () {
    expect(this.renderedComponent.find('form button#add-new-attribute').exists()).toBe(true)
  })
  it('should have text field for name, value and unit', function () {
    expect(this.renderedComponent.find('input[type="text"][name="name"]').exists()).toBe(true)
    expect(this.renderedComponent.find('input[type="text"][name="value"]').exists()).toBe(true)
    expect(this.renderedComponent.find('input[type="text"][name="unit"]').exists()).toBe(true)
  })
  it('should have an Add attribute button', function () {
    expect(this.renderedComponent.find('button#add-new-attribute').exists()).toBe(true)
    expect(this.renderedComponent.find('button#add-new-attribute').text()).toMatch("Add")
  })

  it('should display a table header', function () {
    const wrapper = this.renderedComponent
    return wrapper.vm.$nextTick().then(function () {
      expect(wrapper.find('table thead tr th:first-child').text()).toBe("Name")
      expect(wrapper.find('table thead tr th:nth-child(2)').text()).toBe("Value")
      expect(wrapper.find('table thead tr th:nth-child(3)').text()).toBe("Unit")
      expect(wrapper.find('table thead tr th:nth-child(4)').text()).toBe("Actions")
    })
  })

  it('should display a table of existing attributes', function () {
    const wrapper = this.renderedComponent
    return wrapper.vm.$nextTick().then(function () {
      expect(wrapper.find('table tbody tr:first-child td:first-child').text()).toBe("Luminosity")
      expect(wrapper.find('table tbody tr:first-child td:nth-child(2)').text()).toBe("400")
      expect(wrapper.find('table tbody tr:first-child td:nth-child(3)').text()).toBe("Lux")
      expect(wrapper.find('table tbody tr:nth-child(2) td:first-child').text()).toBe("Contrast")
      expect(wrapper.find('table tbody tr:nth-child(2) td:nth-child(2)').text()).toBe("3000")
      expect(wrapper.find('table tbody tr:nth-child(2) td:nth-child(3)').text()).toBe("Nits")
      expect(wrapper.find('table tbody tr:nth-child(3)').exists()).toBe(false)
    })
  })

  it('should add new attribute to data array', function () {
    const wrapper = this.renderedComponent

    const newAttr = { "name": "Temperature", "value": "45", "unit": "Degree Celsius" }
    const nameField = wrapper.find('input[type="text"][name="name"]')
    const valueField = wrapper.find('input[type="text"][name="value"]')
    const unitField = wrapper.find('input[type="text"][name="unit"]')

    nameField.setValue(newAttr.name)
    valueField.setValue(newAttr.value)
    unitField.setValue(newAttr.unit)

    wrapper.find('button#add-new-attribute').trigger('click')

    return wrapper.vm.$nextTick().then(function () {
      expect(wrapper.vm.attributes.length).toBe(3)
      expect(wrapper.vm.attributes[2].name).toBe(newAttr.name)
      expect(wrapper.vm.attributes[2].value).toBe(newAttr.value)
      expect(wrapper.vm.attributes[2].unit).toBe(newAttr.unit)
    })
  })

  it('should display delete button next to each attributes', function () {
    const wrapper = this.renderedComponent
    return wrapper.vm.$nextTick().then(function () {
      expect(wrapper.find('table tbody tr:first-child td:last-child button[aria-label^="Remove"]').exists()).toBe(true)
      expect(wrapper.find('table tbody tr:last-child td:last-child button[aria-label^="Remove"]').exists()).toBe(true)
    })
  })

  it('should trigger attribute deletion upon clicking delete button', function () {
    const wrapper = this.renderedComponent
    return wrapper.vm.$nextTick().then(function () {
      wrapper.findAll('button[aria-label^="Remove"]').at(1).trigger('click')
      expect(wrapper.vm.attributes.length).toBe(1)
      expect(wrapper.find('table tbody tr:first-child td:first-child').text()).toBe("Luminosity")
    })
  })

  it('should display a combobox if attributes are provided', function() {
    const wrapper = factory({
      propsData: {
        fileAttributes: JSON.parse(JSON.stringify(addedAttributes)),
        availableAttributes: [
          {
            attribute_name: "Age",
            attachable_to_files: true,
          },
          {
            attribute_name: "Weight",
            attachable_to_files: true,
          },
          {
            attribute_name: "Length",
            attachable_to_files: false,
          }
        ]
      }
    })

    expect(wrapper.find('select').exists()).toBe(true)
    const options = wrapper.findAll('select option')
    expect(options.length).toBe(3)
    expect(options.at(1).attributes('value')).toBe("Age")
    expect(options.at(2).attributes('value')).toBe("Weight")
  })
})