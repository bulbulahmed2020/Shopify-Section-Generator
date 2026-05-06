import '../css/app.css'

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    const prompt = document.getElementById('prompt')
    const charCount = document.getElementById('charCount')
    
    // Character counter
    if (prompt && charCount) {
        prompt.addEventListener('input', () => {
            charCount.textContent = prompt.value.length + ' / 2000 characters'
        })
    }
})

// Preset filling function (called from onclick handlers)
window.fillPreset = function(presetPrompt, presetLabel) {
    const prompt = document.getElementById('prompt')
    const charCount = document.getElementById('charCount')
    
    if (prompt) {
        prompt.value = presetPrompt
        prompt.focus()
        
        if (charCount) {
            charCount.textContent = prompt.value.length + ' / 2000 characters'
        }
        
        // Scroll to form
        prompt.scrollIntoView({ behavior: 'smooth', block: 'center' })
    }
}

// Copy to clipboard function (called from onclick handlers)
window.copyToClipboard = function(elementId, button) {
    const element = document.getElementById(elementId)
    if (!element) return
    
    const text = element.textContent

    navigator.clipboard.writeText(text).then(() => {
        const originalText = button.textContent
        button.textContent = '✅ Copied!'
        button.classList.add('copied')

        setTimeout(() => {
            button.textContent = originalText
            button.classList.remove('copied')
        }, 2000)
    }).catch(err => {
        alert('Failed to copy to clipboard')
        console.error(err)
    })
}
