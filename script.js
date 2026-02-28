// script.js - 12 Interasaun Layout

// Kalkula total ba interasaun ida
function kalkulaTotal(interasaunNum) {
    let total = 0;
    let count = 0;
    
    // Hetan checkbox hotu ba interasaun ne'e
    const checkboxes = document.querySelectorAll(
        `.lok-check[data-interasaun="${interasaunNum}"]:checked`
    );
    
    checkboxes.forEach(checkbox => {
        const distansia = parseInt(checkbox.dataset.distansia) || 0;
        total += distansia;
        count++;
    });
    
    // Update total field
    const totalField = document.getElementById(`total_${interasaunNum}`);
    if (totalField) {
        if (count > 0) {
            totalField.value = `${count} lok, ${total}m (${(total/1000).toFixed(2)}km)`;
            totalField.style.backgroundColor = '#ffffcc';
        } else {
            totalField.value = '';
            totalField.style.backgroundColor = '';
        }
    }
    
    // Hatudu mensajen
    if (count > 0) {
        showNotification(
            `Interasaun ${interasaunNum}: ${count} lok, ${total}m (${(total/1000).toFixed(2)}km)`,
            'success'
        );
    } else {
        showNotification('Favor hili lokasaun antes!', 'warning');
    }
}

// Kalkula solusaun (BFS) - kompara hotu 12 interasaun
function kalkulaSolusaun() {
    // Array atu rai total kada interasaun
    const totals = [];
    let hasData = false;
    
    // Kalkula total ba kada interasaun
    for (let i = 1; i <= 12; i++) {
        const total = getTotalInterasaun(i);
        totals.push(total);
        if (total > 0) hasData = true;
    }
    
    // Verifica se iha dadus
    if (!hasData) {
        showNotification('Favor hili lokasaun antes atu halo kalkulu!', 'error');
        return;
    }
    
    // Simula progress
    simulateProgress();
    
    // Hetan dalan besik
    let minDistansia = Infinity;
    let dalanBesik = -1;
    
    totals.forEach((total, index) => {
        if (total > 0 && total < minDistansia) {
            minDistansia = total;
            dalanBesik = index + 1;
        }
    });
    
    // Hatudu rezultadu
    setTimeout(() => {
        displayResults(totals, dalanBesik, minDistansia);
    }, 2000);
}

// Hetan total interasaun
function getTotalInterasaun(interasaunNum) {
    let total = 0;
    
    const checkboxes = document.querySelectorAll(
        `.lok-check[data-interasaun="${interasaunNum}"]:checked`
    );
    
    checkboxes.forEach(checkbox => {
        total += parseInt(checkbox.dataset.distansia) || 0;
    });
    
    return total;
}

// Simula progress bar
function simulateProgress() {
    const progressBar = document.getElementById('progressBar');
    if (!progressBar) return;
    
    let progress = 0;
    progressBar.style.width = '0%';
    progressBar.textContent = '0%';
    
    const interval = setInterval(() => {
        progress += 20;
        progressBar.style.width = progress + '%';
        progressBar.textContent = progress + '%';
        
        if (progress >= 100) {
            clearInterval(interval);
        }
    }, 400);
}

// Hatudu rezultadu
function displayResults(totals, dalanBesik, minDistansia) {
    const resultArea = document.getElementById('resultaduTotal');
    if (!resultArea) return;
    
    let resultado = '╔════════════════════════════════════════╗\n';
    resultado += '║   REZULTADU ANALIZA BFS (12 ROTA)     ║\n';
    resultado += '╚════════════════════════════════════════╝\n\n';
    
    resultado += 'DISTÁNSIA TOTAL BA KADA INTERASAUN:\n';
    resultado += '─────────────────────────────────────────\n';
    
    totals.forEach((total, index) => {
        const interNum = index + 1;
        const romanNum = getRomanNumeral(interNum);
        const status = total > 0 ? `${total}m (${(total/1000).toFixed(2)}km)` : 'La hili';
        const badge = interNum === dalanBesik ? ' ★ BESIK LIU ★' : '';
        
        resultado += `${interNum.toString().padStart(2)}. Interasaun ${romanNum.padEnd(4)}: ${status}${badge}\n`;
    });
    
    resultado += '\n';
    
    if (dalanBesik > 0 && minDistansia !== Infinity) {
        resultado += '═════════════════════════════════════════\n';
        resultado += '          ★ DALAN BESIK LIU ★\n';
        resultado += '═════════════════════════════════════════\n';
        resultado += `Interasaun: ${dalanBesik} (${getRomanNumeral(dalanBesik)})\n`;
        resultado += `Distánsia: ${minDistansia}m (${(minDistansia/1000).toFixed(2)}km)\n`;
        resultado += '═════════════════════════════════════════\n';
        
        // Hatudu informasaun adisional
        const countLok = document.querySelectorAll(
            `.lok-check[data-interasaun="${dalanBesik}"]:checked`
        ).length;
        resultado += `Lokasaun Total: ${countLok}\n`;
        resultado += `Media Distánsia: ${(minDistansia/countLok).toFixed(2)}m\n`;
    }
    
    resultArea.value = resultado;
}

// Convert numeru ba Roman numeral
function getRomanNumeral(num) {
    const romanNumerals = {
        1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI',
        7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X', 11: 'XI', 12: 'XII'
    };
    return romanNumerals[num] || num.toString();
}

// Rai ba database
function raiDatabase() {
    // Koleta dadus husi kada interasaun
    const dadus = {};
    let ihaDadus = false;
    
    for (let i = 1; i <= 12; i++) {
        const dadusInterasaun = koletaDadusInterasaun(i);
        dadus[`interasaun_${i}`] = dadusInterasaun;
        if (dadusInterasaun) ihaDadus = true;
    }
    
    if (!ihaDadus) {
        showNotification('Favor hili lokasaun antes atu rai dadus!', 'error');
        return;
    }
    
    // Hatudu loading
    const btn = document.querySelector('button[onclick="raiDatabase()"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rai...';
    btn.disabled = true;
    
    // AJAX request
    fetch('ajax_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'rai_dadus_interasaun_hotu',
            dadus: dadus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error atu rai dadus!', 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Koleta dadus husi interasaun
function koletaDadusInterasaun(interasaunNum) {
    let dadus = '';
    let totalDistansia = 0;
    let countLokasaun = 0;
    
    const checkboxes = document.querySelectorAll(
        `.lok-check[data-interasaun="${interasaunNum}"]:checked`
    );
    
    checkboxes.forEach(checkbox => {
        const distansia = parseInt(checkbox.dataset.distansia) || 0;
        totalDistansia += distansia;
        countLokasaun++;
    });
    
    if (countLokasaun > 0) {
        dadus = `${countLokasaun} lok, ${totalDistansia}m`;
    }
    
    return dadus;
}

// Seleksiona hotu
function seleksionaHotu() {
    let totalChecked = 0;
    
    document.querySelectorAll('.lok-check').forEach(checkbox => {
        if (!checkbox.checked) {
            checkbox.checked = true;
            totalChecked++;
        }
    });
    
    showNotification(`Seleksiona ${totalChecked} lokasaun foun!`, 'info');
}

// Deseleksiona hotu
function deseleksionaHotu() {
    let totalUnchecked = 0;
    
    document.querySelectorAll('.lok-check:checked').forEach(checkbox => {
        checkbox.checked = false;
        totalUnchecked++;
    });
    
    // Limpa total fields
    for (let i = 1; i <= 12; i++) {
        const field = document.getElementById(`total_${i}`);
        if (field) {
            field.value = '';
            field.style.backgroundColor = '';
        }
    }
    
    // Limpa resultado
    const resultArea = document.getElementById('resultaduTotal');
    if (resultArea) {
        resultArea.value = '';
    }
    
    // Reset progress bar
    const progressBar = document.getElementById('progressBar');
    if (progressBar) {
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
    }
    
    showNotification(`Deseleksiona ${totalUnchecked} lokasaun`, 'warning');
}

// Hatudu notifikasaun
function showNotification(message, type) {
    // Kria notification element
    const notification = document.createElement('div');
    
    let alertClass = 'alert-info';
    if (type === 'error') alertClass = 'alert-danger';
    else if (type === 'success') alertClass = 'alert-success';
    else if (type === 'warning') alertClass = 'alert-warning';
    
    notification.className = `alert ${alertClass} alert-dismissible fade show`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.innerHTML = `
        <strong>${message}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Automatiku remove depois 3 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Event listener ba checkbox
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.lok-check');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Hatudu feedback visual
            if (this.checked) {
                this.parentElement.style.backgroundColor = 'rgba(255,255,200,0.3)';
            } else {
                this.parentElement.style.backgroundColor = '';
            }
        });
    });
    
    console.log('✓ Sistema 12 Interasaun inisializadu ho susesu!');
    console.log('✓ Total Interasaun: 12');
});