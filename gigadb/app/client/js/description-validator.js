// Note: generally dynamic messages like the ones in this widget should be associated to its input via aria-describedby, we're not doing it here because the input is using a bootstrap tooltip, which messes up with this behavior

const WORD_COUNT_WARNING = 250
const WORD_COUNT_ERROR = 500

/**
 * Counts words in a string, handling whitespace and empty strings
 * @param {string} str - The string to count words in
 * @returns {number} The word count
 */
function countWords(str) {
	return str.trim() ? str.trim().split(/\s+/).length : 0
}

/**
 * Creates a message container with appropriate ARIA role
 * @param {string} type - The type of message (warning/error)
 * @param {string} role - The ARIA role (status/alert)
 * @param {string} additionalClass - Optional additional CSS class
 * @returns {object} Object containing container and content elements
 */
function createMessageContainer(type, role, additionalClass = '') {
	const $container = $('<div>')
		.addClass(`description-${type}-container ${additionalClass}`)
		.attr('role', role)
		.attr('aria-live', role === 'alert' ? 'assertive' : 'polite')

	const $content = $('<div>')
		.addClass(`description-${type}-content`)
		.appendTo($container)

	return { container: $container, content: $content }
}

/**
 * Initializes word count validation for description fields
 * @param {string} selector - CSS selector for target textarea elements
 */
function initDescriptionValidator(selector) {
	$(selector).each(function () {
		const $textarea = $(this)
		const $form = $textarea.closest('form')
		const $container = $textarea.parent()
		const hasTooltip = $textarea.attr('data-toggle') === 'tooltip'

		const $wordCount = $('<div>').addClass('word-count-display').attr('role', 'status')
		const warning = createMessageContainer('warning', 'status', 'warning-message')
		const error = createMessageContainer('error', 'alert', 'error-message')

		$container.append($wordCount, warning.container, error.container)

		const wordCountId = `word-count-${Date.now()}`
		const warningId = `warning-${Date.now()}`
		const errorId = `error-${Date.now()}`

		$wordCount.attr('id', wordCountId)
		warning.container.attr('id', warningId)
		error.container.attr('id', errorId)

		// Only set initial aria-describedby if no tooltip
		if (!hasTooltip) {
			$textarea.attr('aria-describedby', wordCountId)
		}

		function updateWordCount() {
			const count = countWords($textarea.val())
			$wordCount.text(`${count} words`)

			const descriptors = [wordCountId]

			if (count > WORD_COUNT_WARNING && count <= WORD_COUNT_ERROR) {
				warning.content.text(`Warning: Your description exceeds ${WORD_COUNT_WARNING} words.`)
				error.content.text('')
				descriptors.push(warningId)
			}
			else if (count > WORD_COUNT_ERROR) {
				warning.content.text('')
				error.content.text(`Error: Maximum allowed words are ${WORD_COUNT_ERROR}.`)
				descriptors.push(errorId)
			}
			else {
				warning.content.text('')
				error.content.text('')
			}

			// Only update aria-describedby if no tooltip
			if (!hasTooltip) {
				$textarea.attr('aria-describedby', descriptors.join(' '))
			}
			$textarea.attr('aria-invalid', count > WORD_COUNT_ERROR ? 'true' : 'false')
		}

		$textarea.on('input', updateWordCount)
		updateWordCount()

		// Handle form submission
		$form.on('submit', function(e) {
			const count = countWords($textarea.val())
			const isPublished = $form.find('[name="Dataset[upload_status]"]').val() === 'Published'

			if (count > WORD_COUNT_ERROR && !isPublished) {
				e.preventDefault()
				$textarea.focus()
				error.content.text(`Error: Maximum allowed words are ${WORD_COUNT_ERROR}. Please reduce the description length before submitting.`)
			}
		})
	})
}

export { initDescriptionValidator }
