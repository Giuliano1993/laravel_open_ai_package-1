/* Chat */
const textarea = document.querySelector('textarea#istructions')

const text = textarea.value
let newText = text.replaceAll("\n", '<br>')
let newText_rev = newText.replaceAll("Me:", '<strong>Me: </strong>')
let newText_rev_2 = newText_rev.replaceAll("AI:", '<strong>AI: </strong>')

document.querySelector('.conversation').innerHTML = newText_rev_2
