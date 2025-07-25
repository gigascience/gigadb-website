import { describe, expectTypeOf, test } from 'vitest'
import type { PropType } from 'vue'
import ImageEditorStatusDisplay from '../components/ImageEditorStatusDisplay.vue'

interface ImageDimensions {
  width: number
  height: number
}

describe('ImageEditorStatusDisplay types', () => {
  test('prop types are correct', () => {
    expectTypeOf<{
      imageDimensions: PropType<ImageDimensions>
    }>().toMatchTypeOf<typeof ImageEditorStatusDisplay['props']>()
  })
})