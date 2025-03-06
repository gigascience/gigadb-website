function parseDataToLog(data) {
  return data
}

const isProduction = false // process.env.NODE_ENV === 'production'

export function logger(level = 'debug', message, data) {
	if (isProduction) {
		return // Omit logs in production
	}

	const timestamp = new Date().toISOString().slice(0, 19).replace('T', ' ')
	const caller =
		new Error().stack?.split('\n')[2]?.trim().split(' ')[1] || 'Unknown'
	const logEntry = {
		level,
		message,
		timestamp,
		caller,
		data,
	}

	const coloredLevel = level.toUpperCase()

	const formattedMessage =
		`[${logEntry.timestamp}] ` +
		`${coloredLevel} ` +
		`(${logEntry.caller}): ` +
		`${logEntry.message}`

	switch (level) {
		case 'info':
			data
				? console.info(formattedMessage, parseDataToLog(data))
				: console.info(formattedMessage)
			break
		case 'warn':
			data
				? console.warn(formattedMessage, parseDataToLog(data))
				: console.warn(formattedMessage)
			break
		case 'error':
			data
				? console.error(formattedMessage, parseDataToLog(data))
				: console.error(formattedMessage)
			break
		case 'debug':
			data
				? console.debug(formattedMessage, parseDataToLog(data))
				: console.debug(formattedMessage)
			break
		default:
			data
				? console.log(formattedMessage, parseDataToLog(data))
				: console.log(formattedMessage)
	}
}
