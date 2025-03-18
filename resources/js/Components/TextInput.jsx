import { forwardRef, useEffect, useImperativeHandle, useRef } from 'react';

export default forwardRef(function TextInput(
    { type = 'text', className = '', isFocused = false, readOnly = false, ...props },
    ref,
) {
    const localRef = useRef(null);

    useImperativeHandle(ref, () => ({
        focus: () => localRef.current?.focus(),
    }));

    useEffect(() => {
        if (isFocused) {
            localRef.current?.focus();
        }
    }, [isFocused]);

    return (
        <input
            {...props}
            type={type}
            className={
                'p-3 rounded border border-gray-200 bg-white shadow-sm focus:border-sky-300 focus:ring-sky-300 focus-visible:border-sky-300 ' +
                className
            }
            ref={localRef}
            readOnly={readOnly}
        />
    );
});
