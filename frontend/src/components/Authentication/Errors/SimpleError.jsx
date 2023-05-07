/* eslint-disable react/react-in-jsx-scope */
import './SimpleError.css';
import { Error } from '@mui/icons-material';

export function SimpleError({ props }) {
  let validationError = '';
  if (props.status?.validation_errors) {
    validationError =
      props.status.validation_errors[Object.keys(props.status.validation_errors)[0]];
  }

  return (
    <div className="error-container">
      <Error />
      <p className="error-message">
        {props.status.message} {validationError}
      </p>
    </div>
  );
}
