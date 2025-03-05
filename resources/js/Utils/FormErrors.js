let formErrors = []

export function errorsHas (key) {
    if (formErrors.find(error => error.key == key)) {
        return true
    }

    return false
}

export function errorValue(key) {
    return formErrors.find(error => error.key == key).value
}

export function pushErrors(errors) {
    for(let key in errors){
        formErrors.push({
            key: key,
            value: errors[key][0]
        })
    }
}

export function resetErrors() {
    formErrors = []
}
